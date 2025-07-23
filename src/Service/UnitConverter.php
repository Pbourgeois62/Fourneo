<?php

namespace App\Service;

use App\Entity\Unit;
use Doctrine\ORM\EntityManagerInterface;

class UnitConverter
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function convert(float $quantity, Unit $fromUnit, Unit $toUnit): ?float
    {
        if ($fromUnit->getId() === $toUnit->getId()) {
            return $quantity;
        }

        $fromCurrentUnit = $fromUnit;
        $fromFactorToBase = 1.0;
        while ($fromCurrentUnit->getBaseUnit() !== null) {
            if ($fromCurrentUnit->getFactorToBase() === null || $fromCurrentUnit->getFactorToBase() === 0.0) {
                return null;
            }
            $fromFactorToBase *= $fromCurrentUnit->getFactorToBase();
            $fromCurrentUnit = $fromCurrentUnit->getBaseUnit();
        }
        $fromTopBaseUnit = $fromCurrentUnit;

        $toCurrentUnit = $toUnit;
        $toFactorToBase = 1.0;
        while ($toCurrentUnit->getBaseUnit() !== null) {
            if ($toCurrentUnit->getFactorToBase() === null || $toCurrentUnit->getFactorToBase() === 0.0) {
                return null;
            }
            $toFactorToBase *= $toCurrentUnit->getFactorToBase();
            $toCurrentUnit = $toCurrentUnit->getBaseUnit();
        }
        $toTopBaseUnit = $toCurrentUnit;

        if ($fromTopBaseUnit->getId() !== $toTopBaseUnit->getId()) {
            return null;
        }

        $quantityInBaseUnit = $quantity * $fromFactorToBase;

        if ($toFactorToBase === 0.0) {
            return null;
        }
        $convertedQuantity = $quantityInBaseUnit / $toFactorToBase;

        return $convertedQuantity;
    }
}