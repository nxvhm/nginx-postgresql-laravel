<?php

namespace App\Modules\Users\Repository;

use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Modules\Users\Services\Authorization;
use App\Modules\Users\Entity\User;
use App\Modules\Users\Entity\UserAcl;
use App\Modules\Users\Config\Enum\Type;
use App\Modules\Users\Config\Enum\UserLevel;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
	private Authorization $auth;

	public function __construct(ManagerRegistry $registry, Authorization $auth) {
		parent::__construct($registry, User::class);
		$this->auth = $auth;
	}

	public function loadUserByIdentifier(string $identifier): ?User {
		$u = $this->getEntityManager()->createQuery('
			SELECT u FROM App\Modules\Users\Entity\User u
			WHERE u.id = :identifier
			OR u.email = :identifier
		')
		->setParameter('identifier', $identifier)
		->getOneOrNullResult();

		return $u;
	}

	public function findUsers(array $params = [], bool $asQuery = false): mixed {
		$user = $this->auth->getUser();
		$entityManager = $this->getEntityManager();
		$query = $entityManager->createQueryBuilder();
		$query->select('u')
			->from('App\Modules\Users\Entity\User', 'u');

		if(!$this->auth->can('user-view-all')) {
			$userAcls = $entityManager->getRepository(UserAcl::class);
			$query->andWhere('u.id IN (:userIds)')
				->setParameter('userIds', $userAcls->getUserIdsByManager($user->getId()));
		}

		// TODO: INCLUDE RELATION LOADING

		if(!empty($params['searchTerm'])) {
			if(preg_match('/^id:(?<id>\d+)/is', $params['searchTerm'], $match)) {
				$query
					->andWhere('u.id = :id')
					->setParameter('id', $match['id']);
			} else {
				$query->andWhere($query->expr()->andX(
					$query->expr()->orX()
						->add($query->expr()->like('u.name', ':searchTerm'))
						->add($query->expr()->like('u.email', ':searchTerm'))
						->add($query->expr()->like('u.phone', ':searchTerm'))
				))->setParameter('searchTerm', '%'.$params['searchTerm'].'%');
			}
		}

		if(!empty($params['type'])) {
			$types = [];
			if($this->auth->can('user-type-set-merchant'))
				$types[Type::Merchant->value] = Type::Merchant->value;

			if($this->auth->can('user-type-set-staff'))
				$types[Type::Staff->value] = Type::Staff->value;

			if($this->auth->can('user-type-set-agent'))
				$types[Type::Agent->value] = Type::Agent->value;

			if(array_key_exists($params['type'], $types)) {
				$query
					->andWhere('u.type IN (:types)')
					->setParameter('types', $types)
				;
			}
		}

		if(!empty($params['branchId']) && ($this->auth->can('user-view-all') || $this->auth->can('user-view-all-in-selected-branches'))) {
			$query
				->andWhere('u.branch_id IN (:branchId)')
				->setParameter('branchId', $params['branchId'])
			;
		}

		if(!empty($params['roleId'])) {
			$query
				->andWhere('u.role_id IN (:roleId)')
				->setParameter('roleId', $params['roleId'])
			;
		}

		if(!empty($params['prime']))
			$params['prime'] == 'Prime' ? $query->andWhere('u.owner_id IS NULL') : $query->andWhere('u.owner_id IS NOT NULL');

		if(!empty($params['level']) && $params['level'] != UserLevel::ALL->value && $this->auth->can('user-set-owner')) {
			if($params['level'] == UserLevel::L1->value) {
				$query->andWhere('u.owner_id IS NULL');
			} else {
				$query->leftJoin('App\Modules\Users\Entity\User', 'u2', 'u2.id = u.owner_id');

				if($params['level'] == UserLevel::L2->value) {
					$query->andWhere(
						$query->expr()->andX()
							->add('u.owner_id IS NOT NULL')
							->add('u2.id IS NOT NULL')
							->add('u2.owner_id IS NULL')
					);
				} elseif($params['level'] == UserLevel::L3->value) {
					$query->andWhere(
						$query->expr()->andX()
							->add('u.owner_id IS NOT NULL')
							->add('u2.owner_id IS NOT NULL')
					);
				}
			}
		}

		if(!empty($params['tsysAgentId']) && $this->auth->can('user-set-tsys-agent-id')) {
			$query
				->innerJoin(
					'App\Modules\Users\Entity\UserTsysAgent', 'uta',
					'WITH',
					'uta.user_id = u.id AND uta.agentId LIKE :tsysAgentId'
				)
				->setParameter('tsysAgentId', '%'.$params['tsysAgentId'].'%')
			;
		}

		return $asQuery ? $query->getQuery() : $query->getQuery()->getResult();
	}

}
