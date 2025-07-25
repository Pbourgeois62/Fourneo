<?php

namespace App\Twig\Components\Form;

use App\Entity\Recipe;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Step;
use App\Form\addStepsToRecipeForm;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\RecipeCostCalculator;

#[AsLiveComponent]
class addStepsToRecipe extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use LiveCollectionTrait;

    private RecipeCostCalculator $recipeCostCalculator;
    private EntityManagerInterface $entityManager;

    public function __construct(
        RecipeCostCalculator $recipeCostCalculator,
        EntityManagerInterface $entityManager
    ) {
        $this->recipeCostCalculator = $recipeCostCalculator;
        $this->entityManager = $entityManager;
    }

    #[LiveProp(writable: true)]
    public ?Recipe $recipe = null;

    protected function instantiateForm(): FormInterface
    {
        $this->ensureStepNumbersAreSet();
        return $this->createForm(addStepsToRecipeForm::class, $this->recipe);
    }

    #[LiveAction]
    public function save(CategoryRepository $categoryRepository)
    {
        $this->submitForm();

        if (!$this->getForm()->isValid()) {
            $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire.');
            return;
        }

        $recipe = $this->getForm()->getData();
        $isSubRecipe = $this->getForm()->get('isSubRecipe')->getData();
        $isSalable = $this->getForm()->get('isSalable')->getData();

        $product = $recipe->getProductResult();
        if ($product === null) {
            $product = new Product();
        }
        
        $product->setRecipe($recipe);

        if ($product->getId() === null) {
            $this->entityManager->persist($product);
        }

        $categorySalable = $categoryRepository->findOneBy(['name' => 'a-vendre']);
        if ($categorySalable === null) {
            $categorySalable = new Category();
            $categorySalable->setName('a-vendre');
            $this->entityManager->persist($categorySalable);
        }

        $categorySubRecipe = $categoryRepository->findOneBy(['name' => 'sous-recette']);
        if ($categorySubRecipe === null) {
            $categorySubRecipe = new Category();
            $categorySubRecipe->setName('sous-recette');
            $this->entityManager->persist($categorySubRecipe);
        }

        if ($isSalable) {
            if (!$product->getCategories()->contains($categorySalable)) {
                $product->addCategory($categorySalable);
            }
        } else {
            if ($product->getCategories()->contains($categorySalable)) {
                $product->removeCategory($categorySalable);
            }
        }

        if ($isSubRecipe) {
            if (!$product->getCategories()->contains($categorySubRecipe)) {
                $product->addCategory($categorySubRecipe);
            }
        } else {
            if ($product->getCategories()->contains($categorySubRecipe)) {
                $product->removeCategory($categorySubRecipe);
            }
        }

        $product->setName($recipe->getName());

        $batchCost = $this->recipeCostCalculator->calculateTotalCost($recipe);

        if ($batchCost !== null && $recipe->getYield() !== null && $recipe->getYield() > 0 && $recipe->getYieldUnit() !== null) {
            $costPerUnit = $batchCost / $recipe->getYield();
            $product->setPrice($costPerUnit);
            $product->setPriceUnit($recipe->getYieldUnit());
        } else {
            $product->setPrice(0.0);
            $product->setPriceUnit(null);
            $this->addFlash('warning', 'Impossible de calculer le prix unitaire du produit. Assurez-vous que le rendement et son unité sont définis et que le rendement est supérieur à zéro.');
        }

        $this->reindexSteps();

        $this->entityManager->persist($recipe);
        $this->entityManager->flush();

        $this->addFlash('success', 'Recette enregistrée avec succès !');

        return $this->redirectToRoute('recipe_show', ['id' => $recipe->getId()]);
    }

    public function preAddLiveCollectionItem(string $collectionName, object $item): void
    {
        if ($collectionName === 'steps' && $item instanceof Step) {
            $item->setRecipe($this->recipe);
        }
    }

    public function postHydrateLiveCollection(): void
    {
        $this->reindexSteps();
    }

    private function reindexSteps(): void
    {
        $stepNumber = 1;
        foreach ($this->recipe->getSteps()->getValues() as $step) {
            if ($step instanceof Step) {
                $step->setNumber($stepNumber++);
            }
        }
    }

    private function ensureStepNumbersAreSet(): void
    {
        $this->reindexSteps();
    }
}