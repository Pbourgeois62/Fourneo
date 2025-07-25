<?php

namespace App\Service;

use App\Entity\Recipe;

class RecipeAnalyzer
{
    /**
     * Calcule les quantités totales d'ingrédients nécessaires pour une recette donnée,
     * en prenant en compte les sous-recettes de manière récursive.
     *
     * @param Recipe $recipe L'objet recette à analyser.
     * @param float $quantityNeeded La quantité de cette recette spécifique qui est nécessaire (par exemple, nombre de gâteaux).
     * @return array Contient les 'ingredients' agrégés (ID du produit ingredient-ID de l'unité => [name, quantity, unitName]).
     */
    public function calculateTotalIngredients(Recipe $recipe, float $quantityNeeded): array
    {
        $ingredients = [];

        $scalingFactor = 0;
        if ($recipe->getYield() > 0) {
            $scalingFactor = $quantityNeeded / $recipe->getYield();
        } else {
            $scalingFactor = $quantityNeeded > 0 ? 1 : 0;
        }

        foreach ($recipe->getSteps() as $step) {
            foreach ($step->getStepProducts() as $stepProduct) {
                $ingredientProduct = $stepProduct->getProduct();
                $unit = $stepProduct->getUnit();
                $quantityPerBatch = $stepProduct->getQuantity();

                if ($ingredientProduct && $ingredientProduct->getRecipe() !== null) {
                    $subRecipeQuantityNeeded = $quantityPerBatch * $scalingFactor;

                    // Appel récursif à la même méthode de ce service
                    $subRecipeTotals = $this->calculateTotalIngredients($ingredientProduct->getRecipe(), $subRecipeQuantityNeeded);

                    foreach ($subRecipeTotals as $subIngredientId => $subData) {
                        if (!isset($ingredients[$subIngredientId])) {
                            $ingredients[$subIngredientId] = $subData;
                        } else {
                            $ingredients[$subIngredientId]['quantity'] += $subData['quantity'];
                        }
                    }
                } elseif ($ingredientProduct) {
                    // L'ingrédient est un produit direct (matière première)
                    $totalIngredientQuantity = $quantityPerBatch * $scalingFactor;
                    // Crée un ID unique pour l'ingrédient combinant son ID et l'ID de l'unité
                    $ingredientId = $ingredientProduct->getId() . '-' . ($unit ? $unit->getId() : 'no-unit');

                    if (!isset($ingredients[$ingredientId])) {
                        $ingredients[$ingredientId] = [
                            'name' => $ingredientProduct->getName(),
                            'quantity' => 0,
                            'unitName' => $unit ? $unit->getName() : 'N/A',
                        ];
                    }
                    $ingredients[$ingredientId]['quantity'] += $totalIngredientQuantity;
                }
            }
        }

        return $ingredients;
    }

    /**
     * Calcule la durée totale de production pour une liste de recettes,
     * en s'assurant que chaque recette n'est comptée qu'une seule fois.
     *
     * @param iterable<Recipe> $recipes La collection de recettes.
     * @return int La durée totale en minutes.
     */
    public function calculateTotalProductionDuration(iterable $recipes): int
    {
        $totalDuration = 0;
        $processedRecipeIds = [];

        foreach ($recipes as $recipe) {
            if ($recipe && !in_array($recipe->getId(), $processedRecipeIds)) {
                $totalDuration += $recipe->getTotalDuration();
                $processedRecipeIds[] = $recipe->getId();
            }
        }
        return $totalDuration;
    }
}