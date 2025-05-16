<?php

namespace App\Modules\Users\Entity;

use App\Config\Enum\Themes;
use App\Config\Enum\YesNo;
use App\Modules\Users\Config\Enum\Active;
use App\Modules\Users\Config\Enum\TwoStepVerification;
use App\Modules\Users\Config\Enum\Type;
use App\Modules\Users\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;


#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 128)]
    private ?string $name = null;

    #[ORM\Column(length: 128)]
    private ?string $email = null;

    #[ORM\Column(length: 32)]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $passwordChangedOn = null;

    #[ORM\Column(enumType: TwoStepVerification::class)]
    private ?TwoStepVerification $twoStepVerification = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $role_id = null;

    #[ORM\Column(enumType: Type::class)]
    private ?Type $type = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $autoDeactivationDate = null;

    #[ORM\Column(nullable: true, enumType: Active::class)]
    private ?Active $Active = null;

    #[ORM\Column(enumType: YesNo::class)]
    private ?YesNo $riskLiability = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $timezone = null;

    #[ORM\Column(nullable: true, enumType: Themes::class)]
    private ?Themes $themeMode = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $branch_id = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $owner_id = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $createdBy_id = null;

		#[ORM\ManyToOne(targetEntity: Role::class, inversedBy: 'users')]
		private Role $role;

		#[ORM\OneToOne(targetEntity: User::class)]
		#[ORM\JoinColumn(name: 'createdBy_id', referencedColumnName: 'id')]
		private User|null $createdBy;

		#[ORM\OneToOne(targetEntity: User::class)]
		#[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'id')]
		private User|null $owner;

		#[ORM\OneToMany(targetEntity: User::class, mappedBy: 'createdBy')]
		#[ORM\JoinColumn(name: 'id', referencedColumnName: 'createdBy_id')]
		private Collection $users;

		private array $roles = [];

		public function __construct() {
			$this->users = new ArrayCollection();
		}

		public function getUsers(): Collection {
			return $this->users;
		}

		public function getCreatedBy(): User|null {
			return $this->createdBy;
		}

		public function getOwner(): User|null {
			return $this->owner;
		}

    public function getUserIdentifier(): string {
			return (string) $this->email;
    }

    public function eraseCredentials(): void {
			$this->password = null;
    }

    public function needsRehash(string $hashedPassword): bool {
      return false;
    }

		public function getRole(): ?Role {
			return $this->role;
		}

    /**
     * @see UserInterface
     */
    public function getRoles(): array {
			return $this->roles;
    }

    public function setRoles(array $roles): self
    {
			$this->roles = $roles;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getPasswordChangedOn(): ?\DateTimeInterface
    {
        return $this->passwordChangedOn;
    }

    public function setPasswordChangedOn(?\DateTimeInterface $passwordChangedOn): static
    {
        $this->passwordChangedOn = $passwordChangedOn;

        return $this;
    }

    public function getTwoStepVerification(): ?TwoStepVerification
    {
        return $this->twoStepVerification;
    }

    public function setTwoStepVerification(TwoStepVerification $twoStepVerification): static
    {
        $this->twoStepVerification = $twoStepVerification;

        return $this;
    }

    public function getRoleId(): ?string
    {
        return $this->role_id;
    }

    public function setRoleId(string $role_id): static
    {
        $this->role_id = $role_id;

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

    public function getAutoDeactivationDate(): ?\DateTimeInterface
    {
        return $this->autoDeactivationDate;
    }

    public function setAutoDeactivationDate(?\DateTimeInterface $autoDeactivationDate): static
    {
        $this->autoDeactivationDate = $autoDeactivationDate;

        return $this;
    }

    public function getActive(): ?Active
    {
        return $this->Active;
    }

    public function setActive(?Active $Active): static
    {
        $this->Active = $Active;

        return $this;
    }

    public function getRiskLiability(): ?YesNo
    {
        return $this->riskLiability;
    }

    public function setRiskLiability(YesNo $riskLiability): static
    {
        $this->riskLiability = $riskLiability;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getThemeMode(): ?Themes
    {
        return $this->themeMode;
    }

    public function setThemeMode(?Themes $themeMode): static
    {
        $this->themeMode = $themeMode;

        return $this;
    }

    public function getBranchId(): ?string
    {
        return $this->branch_id;
    }

    public function setBranchId(string $branch_id): static
    {
        $this->branch_id = $branch_id;

        return $this;
    }

    public function getOwnerId(): ?string
    {
        return $this->owner_id;
    }

    public function setOwnerId(?string $owner_id): static
    {
        $this->owner_id = $owner_id;

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
}
