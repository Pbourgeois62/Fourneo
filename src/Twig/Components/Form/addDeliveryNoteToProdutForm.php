<?php

namespace App\Twig\Components\Form;

use App\Entity\Product;
use App\Entity\DeliveryNote;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use App\Form\LinkDeliveryNoteToProductForm;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

#[AsLiveComponent]
class addDeliveryNoteToProdutForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public function __construct() {}

    #[LiveProp(writable: true)]
    public ?Product $product = null;

    #[LiveProp]
    public ?string $singleUploadFilename = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(LinkDeliveryNoteToProductForm::class);
    }

    #[LiveAction]
    public function saveDeliveryNote(EntityManagerInterface $entityManager): ?Response
    {
        $this->submitForm();

        if (!$this->getForm()->isValid()) {
            $this->addFlash('error', 'Il y a eu des erreurs dans la soumission du formulaire.');
            return null;
        }

        $form = $this->getForm();
        $isExisting = $form->get('isExisting')->getData();
        $selectedDeliveryNote = null;

        if ($isExisting) {
            $selectedDeliveryNote = $form->get('deliveryNoteChoice')->getData();

            if (!$selectedDeliveryNote instanceof DeliveryNote) {
                $this->addFlash('error', 'Aucun bon de livraison existant n\'a été sélectionné. Veuillez choisir un bon dans la liste.');
                return null;
            }
        } else {
            $selectedDeliveryNote = $form->get('DeliveryNote')->getData();

            if (!$selectedDeliveryNote instanceof DeliveryNote) {
                $this->addFlash('error', 'Impossible de créer un nouveau bon de livraison. Les données sont manquantes ou invalides.');
                return null;
            }

            $entityManager->persist($selectedDeliveryNote);
        }

        if ($selectedDeliveryNote && $this->product) {
            $selectedDeliveryNote->addProduct($this->product);

            $entityManager->persist($selectedDeliveryNote);
            $entityManager->persist($this->product);

            $entityManager->flush();

            $this->addFlash('success', 'Bon de livraison lié au produit avec succès !');
            return $this->redirectToRoute('product_index');
        } else {
            $this->addFlash('error', 'Échec de la liaison du bon de livraison au produit. Vérifiez que le produit et le bon de livraison sont corrects.');
            return null;
        }
    }

    // #[LiveAction]
    // public function linkDeliveryNoteToProduct(#[LiveArg('itemName')] string $deliveryNoteNumber,EntityManagerInterface $entityManager)
    // {
    //     $deliveryNote = $entityManager->getRepository(DeliveryNote::class)->findOneBy(['number' => $deliveryNoteNumber]);
    //     $deliveryNote->addProduct($this->product);
    //     $entityManager->persist($deliveryNote);

    //     $entityManager->flush();

    //     $this->addFlash('success', 'Événement de vente lié avec succès !');

    //     return $this->redirectToRoute('product_index');
    // }
}