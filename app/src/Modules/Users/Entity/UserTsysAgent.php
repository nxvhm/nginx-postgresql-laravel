<?php

namespace App\Modules\Users\Entity;

use App\Config\Enum\MerchantActivation;
use App\Config\Enum\RiskLevel;
use App\Modules\Users\Repository\UserTsysAgentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[
	ORM\Entity(repositoryClass: UserTsysAgentRepository::class),
	ORM\Table('user_tsys_agent')
]
class UserTsysAgent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $user_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $acquirer_bin_id = null;

    #[ORM\Column(nullable: true, enumType: RiskLevel::class)]
    private ?RiskLevel $type = null;

    #[ORM\Column(length: 32)]
    private ?string $agentId = null;

    #[ORM\Column(enumType: MerchantActivation::class)]
    private ?MerchantActivation $merchantActivation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
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

    public function getAcquirerBinId(): ?int
    {
        return $this->acquirer_bin_id;
    }

    public function setAcquirerBinId(?int $acquirer_bin_id): static
    {
        $this->acquirer_bin_id = $acquirer_bin_id;

        return $this;
    }

    public function getType(): ?RiskLevel
    {
        return $this->type;
    }

    public function setType(?RiskLevel $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getAgentId(): ?string
    {
        return $this->agentId;
    }

    public function setAgentId(string $agentId): static
    {
        $this->agentId = $agentId;

        return $this;
    }

    public function getMerchantActivation(): ?MerchantActivation
    {
        return $this->merchantActivation;
    }

    public function setMerchantActivation(MerchantActivation $merchantActivation): static
    {
        $this->merchantActivation = $merchantActivation;

        return $this;
    }
}
