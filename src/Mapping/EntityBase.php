<?php

namespace App\Mapping;

use App\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
class EntityBase
{
    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?DateTime $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?DateTime $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    protected ?User $createdUser = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    protected ?User $updateUser = null;

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updatedTimestamps(): void
    {
        $dateTimeNow = new DateTime('now');

        $this->setUpdatedAt($dateTimeNow);

        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt($dateTimeNow);
        }
    }

    public function setUpdatedAt(?DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setCreatedAt(?DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * Note: Cette méthode nécessite que tu passes l'utilisateur manuellement
     * depuis ton contrôleur ou un EventListener.
     */
    public function updatedUserstamps(?User $currentUser): void
    {
        $this->setUpdateUser($currentUser);

        if ($this->getCreatedUser() === null) {
            $this->setCreatedUser($currentUser);
        }
    }

    public function setCreatedUser(?User $createdUser): static
    {
        $this->createdUser = $createdUser;
        return $this;
    }

    public function getCreatedUser(): ?User
    {
        return $this->createdUser;
    }

    public function setUpdateUser(?User $updateUser = null): static
    {
        $this->updateUser = $updateUser;
        return $this;
    }

    public function getUpdateUser(): ?User
    {
        return $this->updateUser;
    }
}