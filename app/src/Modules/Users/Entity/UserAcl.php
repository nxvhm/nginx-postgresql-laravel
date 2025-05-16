<?php

namespace App\Modules\Users\Entity;

use App\Modules\Users\Repository\UserAclRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[
	ORM\Entity(repositoryClass: UserAclRepository::class),
	ORM\Table('user_acl')
]
class UserAcl
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::BIGINT)]
		#[ORM\OneToOne('App\Modules\Users\Entity\User')]
    private ?string $user_id = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $manager_id = null;

    #[ORM\Column(type: Types::BIGINT)]
		#[ORM\ManyToOne('App\Modules\Users\Entity\Permission')]
		private ?string $permission_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?string
    {
        return $this->user_id;
    }

    public function setUserId(string $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getManagerId(): ?string
    {
        return $this->manager_id;
    }

    public function setManagerId(string $manager_id): static
    {
        $this->manager_id = $manager_id;

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
}
