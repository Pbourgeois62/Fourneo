<?php

namespace App\Entity;

use App\Repository\UnitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UnitRepository::class)]
class Unit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    // Renommé de 'factor' à 'factorToBase' pour correspondre au UnitConverter
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $factorToBase = null; // Combien de CETTE unité il y a dans son unité de base (ex: 1 gramme = 0.001 kilogramme)

    #[ORM\ManyToOne(targetEntity: self::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?self $baseUnit = null;

    public function getId(): ?int
    {
        return $this->id;
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

    // Getter renommé de getFactor() à getFactorToBase()
    public function getFactorToBase(): ?float
    {
        return $this->factorToBase;
    }

    // Setter renommé de setFactor() à setFactorToBase()
    public function setFactorToBase(?float $factorToBase): static
    {
        $this->factorToBase = $factorToBase;
        return $this;
    }

    public function getBaseUnit(): ?self
    {
        return $this->baseUnit;
    }

    public function setBaseUnit(?self $baseUnit): static
    {
        $this->baseUnit = $baseUnit;
        return $this;
    }

    // Ajouté pour faciliter le débogage et l'affichage dans les formulaires
    public function __toString(): string
    {
        return $this->name ?? 'Unité inconnue';
    }
}