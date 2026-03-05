<?php

namespace App\Entity;

use App\Repository\PageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Mapping\EntityBase;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: PageRepository::class)]
#[UniqueEntity(fields: ['slug'], message: 'This slug is already in use.')]
class Page extends EntityBase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $affichage = null;

    #[ORM\Column]
    private bool $active = true;

    #[ORM\ManyToOne(targetEntity: Parametres::class, inversedBy: 'pages')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Parametres $menu = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
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

    public function getAffichage(): ?string
    {
        return $this->affichage;
    }

    public function setAffichage(?string $affichage): static
    {
        $this->affichage = $affichage;
        return $this;
    }

    public function getMenu(): ?Parametres
    {
        return $this->menu;
    }

    public function setMenu(?Parametres $menu): static
    {
        $this->menu = $menu;
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