<?php

namespace App\Modules\Users\Repository;

use App\Modules\Users\Entity\Permission;
use App\Modules\Users\Entity\Role;
use App\Modules\Users\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Permission>
 */
class PermissionRepository extends ServiceEntityRepository {

	public function __construct(ManagerRegistry $registry, private EntityManagerInterface $entityManager) {
		parent::__construct($registry, Permission::class);
	}

	public function findByRole(Role $role): mixed {
		return $this->entityManager->createQueryBuilder()
			->select([
				'p.id',
				'p.name',
				'p.description',
				'p.group',
				'p.rule_id',
				'rp.use',
				'rp.forbiddenWhenLoggedInAs',
				'rp.reassign',
				'rp.subUsersAutoApply'
			])
			->from('App\Modules\Users\Entity\Permission', 'p')
			->innerJoin('App\Modules\Users\Entity\RolePermission', 'rp', 'WITH', 'rp.permission_id = p.id AND rp.role_id = :role_id')
			->setParameter('role_id', $role->getId())
			->getQuery()
			->getResult();
	}

	public function findUserExplicitPermissions(User $user) {
		return $this->entityManager->createQueryBuilder()
			->select([
				'p.id',
				'p.name',
				'p.description',
				'p.group',
				'p.rule_id',
				'up.use',
				'up.forbiddenWhenLoggedInAs',
				'up.reassign',
			])
			->from('App\Modules\Users\Entity\UserPermission', 'up')
			->innerJoin('App\Modules\Users\Entity\Permission', 'p', 'WITH', 'up.permission_id = p.id')
			->where('up.user_id = :userId')
			->setParameter('userId', $user->getId())
			->getQuery()
			->getResult();
	}
}
