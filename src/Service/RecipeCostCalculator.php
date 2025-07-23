<?php

namespace App\Service;

use App\Entity\Recipe;
use Psr\Log\LoggerInterface; // N'oubliez pas l'use

class RecipeCostCalculator
{
    private UnitConverter $unitConverter;
    private LoggerInterface $logger; // Injection du logger

    public function __construct(UnitConverter $unitConverter, LoggerInterface $logger)
    {
        $this->unitConverter = $unitConverter;
        $this->logger = $logger;
    }

    /**
     * Calcule le coût total d'une recette.
     *
     * @param Recipe $recipe La recette à calculer.
     * @param array $visitedRecipeIds Tableau des IDs de recettes déjà visitées pour détecter les cycles.
     * @return float|null Le coût total ou null si une erreur irrécupérable survient (ex: cycle détecté).
     */
    public function calculateTotalCost(Recipe $recipe, array $visitedRecipeIds = []): ?float
    {
        // Détection de cycle
        if (in_array($recipe->getId(), $visitedRecipeIds)) {
            $this->logger->error(sprintf(
                'Cycle détecté dans les recettes. La recette "%s" (ID: %d) est déjà en cours de calcul. Impossible de calculer le coût.',
                $recipe->getName(),
                $recipe->getId()
            ));
            return null; // Ou lancez une exception si vous préférez une erreur fatale
        }

        // Ajoute la recette actuelle à la liste des visitées pour les appels récursifs
        $currentVisitedRecipeIds = array_merge($visitedRecipeIds, [$recipe->getId()]);

        $totalCost = 0.0;

        foreach ($recipe->getSteps() as $step) {
            foreach ($step->getStepProducts() as $stepProduct) {
                $product = $stepProduct->getProduct();
                $quantityInRecipe = $stepProduct->getQuantity();
                $unitInRecipe = $stepProduct->getUnit();

                if ($product->getPriceUnit() !== null && $product->getPrice() !== null) {
                    // C'est un produit "brut" avec un prix direct
                    $pricePerBaseUnit = $product->getPrice();
                    $baseUnit = $product->getPriceUnit();

                    $convertedQuantity = $this->unitConverter->convert($quantityInRecipe, $unitInRecipe, $baseUnit);

                    if ($convertedQuantity === null) {
                        $this->logger->warning(sprintf(
                            'Conversion d\'unité échouée pour le produit "%s" (ID: %d) dans la recette "%s" (ID: %d). Quantité: %s %s vers %s. Cet ingrédient sera ignoré.',
                            $product->getName(),
                            $product->getId(),
                            $recipe->getName(),
                            $recipe->getId(),
                            $quantityInRecipe,
                            $unitInRecipe ? $unitInRecipe->getName() : 'unité inconnue',
                            $baseUnit ? $baseUnit->getName() : 'unité de base inconnue'
                        ));
                        continue; // Passe à l'ingrédient suivant si la conversion échoue
                    }
                    $totalCost += $convertedQuantity * $pricePerBaseUnit;

                } elseif ($product->getRecipe() !== null) {
                    // C'est un produit qui est le résultat d'une autre recette (sous-recette)
                    $subRecipe = $product->getRecipe();
                    // Appel récursif avec la liste des recettes déjà visitées
                    $subRecipeCost = $this->calculateTotalCost($subRecipe, $currentVisitedRecipeIds);

                    if ($subRecipeCost === null || $subRecipe->getYield() === null || $subRecipe->getYieldUnit() === null || $subRecipe->getYield() === 0) {
                        $this->logger->warning(sprintf(
                            'Impossible de calculer le coût de la sous-recette "%s" (ID: %d) utilisée dans la recette "%s" (ID: %d). Coût total: %s, Rendement: %s %s. Cet ingrédient sera ignoré.',
                            $subRecipe->getName(),
                            $subRecipe->getId(),
                            $recipe->getName(),
                            $recipe->getId(),
                            $subRecipeCost === null ? 'null' : (string)$subRecipeCost,
                            $subRecipe->getYield() === null ? 'null' : (string)$subRecipe->getYield(),
                            $subRecipe->getYieldUnit() === null ? 'null' : $subRecipe->getYieldUnit()->getName()
                        ));
                        continue;
                    }

                    $costPerYieldUnit = $subRecipeCost / $subRecipe->getYield();
                    
                    $convertedQuantity = $this->unitConverter->convert($quantityInRecipe, $unitInRecipe, $subRecipe->getYieldUnit());

                    if ($convertedQuantity === null) {
                        $this->logger->warning(sprintf(
                            'Conversion d\'unité échouée pour la sous-recette "%s" (ID: %d) utilisée dans la recette "%s" (ID: %d). Quantité: %s %s vers %s. Cet ingrédient sera ignoré.',
                            $subRecipe->getName(),
                            $subRecipe->getId(),
                            $recipe->getName(),
                            $recipe->getId(),
                            $quantityInRecipe,
                            $unitInRecipe ? $unitInRecipe->getName() : 'unité inconnue',
                            $subRecipe->getYieldUnit() ? $subRecipe->getYieldUnit()->getName() : 'unité de rendement inconnue'
                        ));
                        continue;
                    }
                    $totalCost += $convertedQuantity * $costPerYieldUnit;
                } else {
                    // Cas où le produit n'a ni prix direct ni recette associée
                    $this->logger->warning(sprintf(
                        'Le produit "%s" (ID: %d) utilisé dans la recette "%s" (ID: %d) n\'a ni prix unitaire ni recette associée. Il sera ignoré dans le calcul du coût.',
                        $product->getName(),
                        $product->getId(),
                        $recipe->getName(),
                        $recipe->getId()
                    ));
                    continue;
                }
            }
        }
        return $totalCost;         
    }
}