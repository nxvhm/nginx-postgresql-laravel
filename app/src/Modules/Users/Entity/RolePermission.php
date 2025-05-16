<?php

namespace App\Modules\Users\Entity;

use App\Config\Enum\YesNo;
use App\Modules\Users\Repository\RolePermissionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;

#[
	ORM\Entity(repositoryClass: RolePermissionRepository::class),
	ORM\Table('role_permission')
]
class RolePermission
{
		public function __construct(
			#[Id, Column(type: Types::BIGINT)]
			private int $role_id,
			#[Id, Column(type: Types::BIGINT)]
			private int $permission_id
		){}

		#[ORM\ManyToOne(targetEntity: Role::class)]
		private Role $role;

		public function getRole(): ?Role {
			return $this->role;
		}

		#[Orm\ManyToOne(targetEntity: Permission::class)]
		private Permission $permission;

		public function getPermission(): ?Permission {
			return $this->permission;
		}

    #[ORM\Column(name: '`use`', enumType: YesNo::class, options: ["default" => 'No'])]
    private ?YesNo $use = null;

    #[ORM\Column(enumType: YesNo::class, options: ["default" => 'No'])]
    private ?YesNo $forbiddenWhenLoggedInAs = null;

    #[ORM\Column(enumType: YesNo::class, options: ["default" => 'No'])]
    private ?YesNo $reassign = null;

    #[ORM\Column(enumType: YesNo::class, options: ["default" => 'No'])]
    private ?YesNo $subUsersAutoApply = null;

    public function getRoleId(): ?string
    {
        return $this->role_id;
    }

    public function setRoleId(string $role_id): static
    {
        $this->role_id = $role_id;

        return $this;
    }

    public function getPermissionId(): ?string
    {
        return $this->permission_id;
    }

    public function setPermissionId(string $permission_id): static
    {
        $this->permission_id = $permission_id;

        return $this;
    }

    public function getUse(): ?YesNo
    {
        return $this->use;
    }

    public function setUse(YesNo $use): static
    {
        $this->use = $use;

        return $this;
    }

    public function getForbiddenWhenLoggedInAs(): ?YesNo
    {
        return $this->forbiddenWhenLoggedInAs;
    }

    public function setForbiddenWhenLoggedInAs(YesNo $forbiddenWhenLoggedInAs): static
    {
        $this->forbiddenWhenLoggedInAs = $forbiddenWhenLoggedInAs;

        return $this;
    }

    public function getReassign(): ?YesNo
    {
        return $this->reassign;
    }

    public function setReassign(YesNo $reassign): static
    {
        $this->reassign = $reassign;

        return $this;
    }

    public function getSubUsersAutoApply(): ?YesNo
    {
        return $this->subUsersAutoApply;
    }

    public function setSubUsersAutoApply(YesNo $subUsersAutoApply): static
    {
        $this->subUsersAutoApply = $subUsersAutoApply;

        return $this;
    }
}
