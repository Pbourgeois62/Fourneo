<?php

namespace App\Twig\Components\Form;

use App\Entity\SaleEvent;
use App\Form\SaleEventTypeForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent]
class addProductsForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use LiveCollectionTrait;    

    public function __construct() {}

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(SaleEventTypeForm::class);
    }

    #[LiveAction]
    public function save(EntityManagerInterface $entityManager)
    {
        $this->submitForm();

        if (!$this->getForm()->isValid()) {
            // Handle validation errors, e.g., re-render with errors
            // You might add a flash message or just let the template show errors
            $this->addFlash('error', 'Erreurs de validation, veuillez corriger les champs.');
            return; // Stop execution if form is invalid
        }
        $saleEvent = $this->getForm()->getData();
        $entityManager->persist($saleEvent); // Persist the main SaleEvent entity
        $entityManager->flush();

        $this->addFlash('success', 'Événement de vente enregistré avec succès !');

        // Redirect to the show page, using the ID of the newly saved saleEvent
        return $this->redirectToRoute('sale_event_index');
    }
}
