<?php

namespace App\Modules\Users\Repository;

use App\Modules\Users\Config\Enum\Type;
use App\Modules\Users\Entity\Role;
use App\Modules\Users\Entity\User;
use App\Modules\Users\Services\Authorization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @extends ServiceEntityRepository<Role>
 */
class RoleRepository extends ServiceEntityRepository
{

	public function __construct(ManagerRegistry $registry, private Authorization $auth, private EntityManagerInterface $entityManager) {
		parent::__construct($registry, Role::class);
	}

	public function getBaseRoleQuery(User|int $user, bool $includeUserRole = false): QueryBuilder {
		$user = is_numeric($user) ? $this->entityManager->getRepository(User::class)->findOneBy(['id' => $user]) : $user;
		$query = $this->createQueryBuilder('role');

		$types = [$user->getType()->value];
		if($this->auth->checkAccess($user, 'user-type-set-merchat'))
			$types[] = Type::Merchant->value;

		if($this->auth->checkAccess($user, 'user-type-set-staff'))
			$types[] = Type::Staff->value;

		if($this->auth->checkAccess($user, 'user-type-set-agent'))
			$types[] = Type::Agent->value;

		$and = $query->expr()->andX(
			$query->expr()->orX()
				->add($query->expr()->eq('role.id', ':userRoleId'))
				->add($query->expr()->eq('role.createdBy_id', ':userId'))
		);

		$query
			->andWhere('role.type IN (:roleTypes)')
			->andWhere($and)
			->setParameters(new ArrayCollection([
				new Parameter('userId', $user->getId()),
				new Parameter('roleTypes', $types),
				new Parameter('userRoleId', $user->getRoleId())
			]));

		return $query;
	}

	public function getRole(int|Role $role, bool $root = false): Role {
		if(!$role instanceof Role) {
			if(empty($role = $this->findOneBy(['id' => $role])))
				throw new HttpException(404, 'The requested role could not be found');
		}

		return $root ? (is_numeric($role->getRootId()) ? $role->getRoot() : $role) : $role;
	}
}
