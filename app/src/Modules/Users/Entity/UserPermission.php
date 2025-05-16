<?php

namespace App\Modules\Users\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Config\Enum\YesNo;
use App\Modules\Users\Repository\UserPermissionRepository;

#[
	ORM\Entity(repositoryClass: UserPermissionRepository::class),
	ORM\Table('user_permission')
]
class UserPermission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $user_id = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $permission_id = null;

    #[ORM\Column(enumType: YesNo::class)]
    private ?YesNo $forbiddenWhenLoggedInAs = null;

    #[ORM\Column(enumType: YesNo::class)]
    private ?YesNo $reassign = null;

    #[ORM\Column(name: '`use`', enumType: YesNo::class, options: ["default" => 'No'])]
    private ?YesNo $use = null;

		#[Orm\ManyToOne(targetEntity: Permission::class)]
		#[Orm\JoinColumn(name: 'permission_id', referencedColumnName: 'id')]
		private Permission $permission;

		public function getPermission(): ?Permission {
			return $this->permission;
		}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?string {
        return $this->user_id;
    }

    public function setUserId(string $user_id): static {
        $this->user_id = $user_id;

        return $this;
    }

    public function getPermissionId(): ?string {
        return $this->permission_id;
    }

    public function setPermissionId(string $permission_id): static
    {
        $this->permission_id = $permission_id;

        return $this;
    }

    public function getForbiddenWhenLoggedInAs(): ?YesNo {
        return $this->forbiddenWhenLoggedInAs;
    }

    public function setForbiddenWhenLoggedInAs(YesNo $forbiddenWhenLoggedInAs): static {
        $this->forbiddenWhenLoggedInAs = $forbiddenWhenLoggedInAs;

        return $this;
    }

    public function getReassign(): ?YesNo {
        return $this->reassign;
    }

    public function setReassign(YesNo $reassign): static {
        $this->reassign = $reassign;

        return $this;
    }

		public function getUse(): ?YesNo {
        return $this->use;
    }

    public function setUse(YesNo $use): static {
			$this->use = $use;

			return $this;
    }
}
