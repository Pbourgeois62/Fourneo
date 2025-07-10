<?php

namespace App\Twig\Components\Form;

use App\Entity\Recipe;
use App\Form\addStepsToRecipeForm;
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
class addStepsToRecipe extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use LiveCollectionTrait;

    public function __construct() {}

    #[LiveProp(writable: true)]
    public ?Recipe $recipe = null;

    protected function instantiateForm(): FormInterface
    {        
        return $this->createForm(addStepsToRecipeForm::class, $this->recipe);
    }

    #[LiveAction]
    public function save(EntityManagerInterface $entityManager)
    {
        $this->submitForm();

        if (!$this->getForm()->isValid()) {
            $this->addFlash('error', 'Erreurs de validation, veuillez corriger les champs.');
            return;
        }
        $recipe = $this->getForm()->getData();
        $steps = $recipe->getSteps();
        foreach ($steps as $step) {
            $entityManager->persist($step);
        }
        
        $entityManager->persist($recipe);
        $entityManager->flush();

        $this->addFlash('success', 'Recette enregistrée avec succès !');

        return $this->redirectToRoute('recipe_show', ['id' => $recipe->getId()]);
    }
}
