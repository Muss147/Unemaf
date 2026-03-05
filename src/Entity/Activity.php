<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Mapping\EntityBase; // Assure-toi que cette classe est aussi migrée en Attributes
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
#[UniqueEntity(fields: ['slug'], message: 'This slug is already in use.')]
class Activity extends EntityBase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\ManyToOne(targetEntity: Parametres::class, inversedBy: 'activities')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Parametres $type = null;

    #[ORM\Column]
    private bool $active = true;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateActivity = null;

    #[ORM\OneToOne(targetEntity: Photos::class, cascade: ['persist'])]
    private ?Photos $couverture = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDateActivity(): ?\DateTimeInterface
    {
        return $this->dateActivity;
    }

    public function setDateActivity(\DateTimeInterface $dateActivity): static
    {
        $this->dateActivity = $dateActivity;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getType(): ?Parametres
    {
        return $this->type;
    }

    public function setType(?Parametres $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getCouverture(): ?Photos
    {
        return $this->couverture;
    }

    public function setCouverture(?Photos $couverture): static
    {
        $this->couverture = $couverture;
        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;
        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }
}