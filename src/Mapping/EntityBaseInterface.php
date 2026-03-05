<?php

namespace App\Mapping;

use DateTime;

/**
 * EntityBase Interface
 * * Définit le contrat pour les entités ayant des suivis de timestamps.
 */
interface EntityBaseInterface
{
    /**
     * Met à jour les dates de création et de modification.
     */
    public function updatedTimestamps(): void;

    /**
     * Définit la date de mise à jour.
     */
    public function setUpdatedAt(DateTime $updatedAt): static;

    /**
     * Récupère la date de mise à jour.
     */
    public function getUpdatedAt(): ?DateTime;

    /**
     * Définit la date de création.
     */
    public function setCreatedAt(DateTime $createdAt): static;

    /**
     * Récupère la date de création.
     */
    public function getCreatedAt(): ?DateTime;
}