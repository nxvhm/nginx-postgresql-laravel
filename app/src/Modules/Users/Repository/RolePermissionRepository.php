<?php

namespace App\Modules\Users\Repository;

use App\Modules\Users\Entity\RolePermission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RolePermission>
 */
class RolePermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RolePermission::class);
    }
}
