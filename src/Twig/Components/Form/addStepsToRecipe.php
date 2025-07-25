<?php

namespace App\Twig\Components\Form;

use App\Entity\Recipe;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Step; // <-- N'oublie pas d'importer l'entité Step !
use App\Form\addStepsToRecipeForm; // C'est ton formulaire principal de recette
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollection\LiveCollection; // Pour le type hinting si besoin
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
    use LiveCollectionTrait; // Ceci te donne accès aux méthodes preAddLiveCollectionItem, etc.

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
        // Au moment de l'instanciation du formulaire, assure-toi que les numéros d'étape sont à jour
        // C'est particulièrement utile si tu édites une recette existante.
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
        $isSubRecipe = $this->getForm()->get('isSubRecipe')->getData(); // Assurez-vous que ce champ existe dans addStepsToRecipeForm

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
        
        // Re-indexer une dernière fois avant la persistance pour être sûr
        $this->reindexSteps(); 
        
        $this->entityManager->persist($recipe);
        $this->entityManager->flush();

        $this->addFlash('success', 'Recette enregistrée avec succès !');

        return $this->redirectToRoute('recipe_show', ['id' => $recipe->getId()]);
    }

    /**
     * Méthode appelée par LiveCollectionTrait avant d'ajouter un nouvel élément.
     * C'est l'occasion de le préparer.
     */
    public function preAddLiveCollectionItem(string $collectionName, object $item): void
    {
        if ($collectionName === 'steps' && $item instanceof Step) {
            // Associe la nouvelle étape à la recette parente
            $item->setRecipe($this->recipe);
            // La numérotation sera gérée par reindexSteps() après l'ajout
        }
    }

    /**
     * Méthode appelée par LiveCollectionTrait après qu'un élément a été ajouté ou retiré.
     * C'est le moment idéal pour re-numéroter toutes les étapes.
     */
    public function postHydrateLiveCollection(): void
    {
        $this->reindexSteps();
    }

    /**
     * Assure que les numéros d'étape sont corrects et séquentiels.
     * Appelé après l'ajout ou la suppression d'une étape.
     */
    private function reindexSteps(): void
    {
        $stepNumber = 1;
        // La méthode getValues() est importante car elle renvoie un tableau standard,
        // évitant les problèmes avec la modification directe de la collection Doctrine
        // pendant l'itération.
        foreach ($this->recipe->getSteps()->getValues() as $step) {
            if ($step instanceof Step) { // S'assurer que c'est bien une instance de Step
                $step->setNumber($stepNumber++);
            }
        }
    }

    /**
     * Appelé lors du mount/instantiateForm pour les recettes existantes
     * ou au premier chargement pour une nouvelle recette afin d'initialiser les numéros.
     */
    private function ensureStepNumbersAreSet(): void
    {
        // Si la recette n'a pas encore d'étapes (nouvelle recette), ou si elles sont déjà numérotées,
        // reindexSteps s'assurera que c'est correct.
        $this->reindexSteps();
    }
}