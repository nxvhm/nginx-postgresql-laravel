<?php

namespace App\Modules\Users\Repository;

use App\Modules\Users\Entity\UserAcl;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserAcl>
 */
class UserAclRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
			parent::__construct($registry, UserAcl::class);
    }

	public function getUserIdsByManager(int $managerId, array|int|null $permission = null): array {
		$q = $this->getEntityManager()->createQueryBuilder();
		$q->select([
			'uacl.user_id'
		])
		->from('App\Modules\Users\Entity\UserAcl', 'uacl')
		->andWhere('uacl.manager_id = :managerId')
		->setParameter('managerId', $managerId);

		if(!empty($permission)) {
			is_array($permission)
				? $q->andWhere('uacl.permission_id IN :permission')
				: $q->andWhere('uacl.permission_id = :permission');
			$q->setParameter('uacl.permission', $permission);
		}

		return array_column($q->getQuery()->getScalarResult(), 'user_id');
	}

	public function isManagerTo(int $managerId, int $userId): bool {
		$conn = $this->getEntityManager()->getConnection();
		$sql = "SELECT EXISTS(SELECT * FROM user_acl WHERE manager_id = :managerId AND user_id = :userId)";
		$stmt = $conn->prepare($sql);
		$stmt->bindValue('managerId', $managerId);
		$stmt->bindValue('userId', $userId);

		return (bool) $stmt->executeQuery()->fetchOne();
	}
}
