<?php

namespace App\Twig\Components\Form;

use App\Entity\SaleEvent;
use App\Form\SaleEventTypeForm;
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
class addProductsForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use LiveCollectionTrait;

    public function __construct() {}

    #[LiveProp(writable: true)]
    public ?SaleEvent $saleEvent = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(SaleEventTypeForm::class, $this->saleEvent);
    }

    #[LiveAction]
    public function save(EntityManagerInterface $entityManager)
    {
        $this->submitForm();

        if (!$this->getForm()->isValid()) {
            $this->addFlash('error', 'Erreurs de validation, veuillez corriger les champs.');
            return;
        }
        $saleEvent = $this->getForm()->getData();
        foreach ($saleEvent->getProductEvents() as $productEvent) {
            if ($productEvent->getUnsoldQuantity() === null) {
                $productEvent->updateLotPrice();
                $productEvent->setUnsoldQuantity($productEvent->getQuantity());
            }
        }
        $entityManager->persist($saleEvent);
        $entityManager->flush();

        $this->addFlash('success', 'Événement de vente enregistré avec succès !');

        return $this->redirectToRoute('sale_event_index');
    }
}
