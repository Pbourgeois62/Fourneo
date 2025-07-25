<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\Recipe;

class AllergenCollector
{
    public function collectAllergensForProducts(iterable $products): array
    {
        $uniqueAllergens = [];

        foreach ($products as $product) {
            $this->collectAllergensFromProduct($product, $uniqueAllergens);
        }

        return $uniqueAllergens;
    }

    private function collectAllergensFromProduct(Product $product, array &$collectedAllergens): void
    {
        if ($product->getRecipe() !== null) {
            $recipe = $product->getRecipe();
            foreach ($recipe->getSteps() as $step) {
                foreach ($step->getStepProducts() as $stepProduct) {
                    $ingredientProduct = $stepProduct->getProduct();
                    if ($ingredientProduct) {
                        $this->collectAllergensFromProduct($ingredientProduct, $collectedAllergens);
                    }
                }
            }
        } else {
            foreach ($product->getAllergens() as $allergen) {
                if (!isset($collectedAllergens[$allergen->getId()])) {
                    $collectedAllergens[$allergen->getId()] = $allergen->getName();
                }
            }
        }
    }

    public function collectAllergensFromRecipe(Recipe $recipe): array
    {
        $uniqueAllergens = [];
        foreach ($recipe->getSteps() as $step) {
            foreach ($step->getStepProducts() as $stepProduct) {
                $ingredientProduct = $stepProduct->getProduct();
                if ($ingredientProduct) {
                    $this->collectAllergensFromProduct($ingredientProduct, $uniqueAllergens);
                }
            }
        }
        return $uniqueAllergens;
    }
}