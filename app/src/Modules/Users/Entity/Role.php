<?php

namespace App\Modules\Users\Entity;

use App\Config\Enum\YesNo;
use App\Modules\Users\Config\Enum\Type;
use App\Modules\Users\Repository\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 32)]
    private ?string $name = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $parent_id = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $defaultPage = null;

    #[ORM\Column(nullable: true, enumType: YesNo::class)]
    private ?YesNo $system = null;

    #[ORM\Column(enumType: Type::class)]
    private ?Type $type = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $requiredPermission_id = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $createdBy_id = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $root_id = null;

		#[ORM\OneToMany(targetEntity: User::class, mappedBy: 'role')]
		private Collection $users;

		#[Orm\ManyToOne(targetEntity: Role::class)]
		#[Orm\JoinColumn(name: 'root_id', referencedColumnName: 'id')]
		private Role $root;

		#[Orm\ManyToOne(targetEntity: Role::class)]
		#[Orm\JoinColumn(name: 'parent_id', referencedColumnName: 'id')]
		private Role $parent;

		#[Orm\OneToMany(targetEntity: Role::class, mappedBy: 'parent')]
		#[Orm\JoinColumn(name: 'id', referencedColumnName: 'parent_id')]
		private Collection $roles;

		public function  __construct()
		{
			$this->users = new ArrayCollection();
		}

		public function getUsers(): Collection {
			return $this->users;
		}

		public function getParent(): Role {
			return $this->parent;
		}

		public function setParent(Role $parent): static {
			$this->parent = $parent;
			return $this;
		}

		public function getRoles(): Collection {
			return $this->roles;
		}

		public function setRoles(Collection $roles): static {
			$this->roles = $roles;
			return $this;
		}

		public function getRoot(): Role {
			return $this->root;
		}

		public function setRoot(Role $root): static {
			$this->root = $root;
			return $this;
		}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getParentId(): ?string
    {
        return $this->parent_id;
    }

    public function setParentId(?string $parent_id): static
    {
        $this->parent_id = $parent_id;

        return $this;
    }

    public function getDefaultPage(): ?string
    {
        return $this->defaultPage;
    }

    public function setDefaultPage(?string $defaultPage): static
    {
        $this->defaultPage = $defaultPage;

        return $this;
    }

    public function getSystem(): ?YesNo
    {
        return $this->system;
    }

    public function setSystem(?YesNo $system): static
    {
        $this->system = $system;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(Type $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getRequiredPermissionId(): ?string
    {
        return $this->requiredPermission_id;
    }

    public function setRequiredPermissionId(?string $requiredPermission_id): static
    {
        $this->requiredPermission_id = $requiredPermission_id;

        return $this;
    }

    public function getCreatedById(): ?string
    {
        return $this->createdBy_id;
    }

    public function setCreatedById(?string $createdBy_id): static
    {
        $this->createdBy_id = $createdBy_id;

        return $this;
    }

    public function getRootId(): ?string
    {
        return $this->root_id;
    }

    public function setRootId(?string $root_id): static
    {
        $this->root_id = $root_id;

        return $this;
    }
}
