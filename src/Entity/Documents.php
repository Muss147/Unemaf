<?php

namespace App\Entity;

use App\Repository\DocumentsRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Mapping\EntityBase;

#[ORM\Entity(repositoryClass: DocumentsRepository::class)]
class Documents extends EntityBase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\ManyToOne(targetEntity: Parametres::class, inversedBy: 'documents')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Parametres $type = null;

    #[ORM\OneToOne(targetEntity: Photos::class, cascade: ['persist'])]
    private ?Photos $fichier = null;

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

    public function getType(): ?Parametres
    {
        return $this->type;
    }

    public function setType(?Parametres $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getFichier(): ?Photos
    {
        return $this->fichier;
    }

    public function setFichier(?Photos $fichier): static
    {
        $this->fichier = $fichier;

        return $this;
    }
}