<?php

namespace App\Modules\Users\Entity;

use App\Modules\Users\Repository\PermissionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PermissionRepository::class)]
class Permission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $rule_id = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $parent_id = null;

    #[ORM\Column(name: '`group`', type: Types::STRING, nullable: true)]
    private ?string $group = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getRuleId(): ?string
    {
        return $this->rule_id;
    }

    public function setRuleId(?string $rule_id): static
    {
        $this->rule_id = $rule_id;

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

		public function getGroup(): ?string {
			return $this->group;
		}

		public function setGroup(?string $group): static {
			$this->group = $group;
			return $this;
		}
}
