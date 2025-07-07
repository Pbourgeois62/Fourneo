<?php

namespace App\Twig\Components\Form;

use App\Entity\Recipe;
use App\Form\RecipeForm;
use App\Entity\RecipeProduct;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent]
class addRecipeProduct extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use LiveCollectionTrait;

    public function __construct() {}

    #[LiveProp(writable: true)]
    public ?Recipe $recipe = null;

    protected function instantiateForm(): FormInterface
    {        
        return $this->createForm(RecipeForm::class, $this->recipe);
    }

    #[LiveAction]
    public function save(EntityManagerInterface $entityManager)
    {
        $this->submitForm();

        if (!$this->getForm()->isValid()) {
            $this->addFlash('error', 'Erreurs de validation, veuillez corriger les champs.');
            return;
        }
        $recipeProduct = $this->getForm()->getData();
        
        $entityManager->persist($recipeProduct);
        $entityManager->flush();

        $this->addFlash('success', 'Recette enregistrée avec succès !');

        return $this->redirectToRoute('recipe_index');
    }
}
