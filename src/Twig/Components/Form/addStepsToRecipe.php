<?php

namespace App\Twig\Components\Form;

use App\Entity\Recipe;
use App\Entity\Category;
use App\Entity\Product;
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

        $categoryName = $isSubRecipe ? 'sous-recette' : 'a-vendre';
        $targetCategory = $categoryRepository->findOneBy(['name' => $categoryName]);

        if ($targetCategory === null) {
            $targetCategory = new Category();
            $targetCategory->setName($categoryName);
            $this->entityManager->persist($targetCategory);
        }

        $product = $recipe->getProductResult();

        if ($product === null) {
            $product = new Product();
            $this->entityManager->persist($product);
            $recipe->setProductResult($product);
        }
        
        $product->setName($recipe->getName()); 
        $product->setCategory($targetCategory);

        if ($isSubRecipe) {
            $product->setRecipe($recipe); 
        } else {
            $product->setRecipe(null); 
        }

        if ($product !== null) {
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
        }
        
        $this->entityManager->persist($recipe);
        $this->entityManager->flush();

        $this->addFlash('success', 'Recette enregistrée avec succès !');

        return $this->redirectToRoute('recipe_show', ['id' => $recipe->getId()]);
    }
}