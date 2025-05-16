<?php

namespace App\Modules\Users\Services;

use App\Modules\Users\Entity\Role;
use App\Modules\Users\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;

class RoleService {

	private RoleRepository $repo;
	public function __construct(private EntityManagerInterface $em) {
		$this->repo = $em->getRepository(Role::class);
	}

	public function getRepository(): RoleRepository {
		return $this->repo;
	}

	public function isParent(Role|int $role, Role|int $child, bool $isEqual = false): bool {
		$role = $this->repo->getRole($role);
		$child = $this->repo->getRole($child);
		$conn = $this->em->getConnection();
		$sql = "WITH RECURSIVE roleCTE AS (
			(
				SELECT `rc`.`id`, `rc`.`parent_id`
				FROM `role` `rc`
				WHERE `rc`.`id` = :childId
			)
			UNION
			(
				SELECT `rp`.`id`, `rp`.`parent_id`
				FROM `roleCTE`
				INNER JOIN `role` `rp` ON rp.id = roleCTE.parent_id
			)
		)
		SELECT * FROM `roleCTE` WHERE (`id` = :parentId)".(!$isEqual ? " AND (NOT (`id` = :childId))" : null);

		$stmt = $conn->prepare($sql);
		$stmt->bindValue('childId', $child->getId());
		$stmt->bindValue('parentId', $role->getId());


		$result = $stmt->executeQuery();
		return !empty($result->fetchAssociative());
	}
}
