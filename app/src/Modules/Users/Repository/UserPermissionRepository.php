<?php

namespace App\Modules\Users\Repository;

use App\Modules\Users\Entity\UserPermission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserPermission>
 */
class UserPermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
      parent::__construct($registry, UserPermission::class);
    }
}
