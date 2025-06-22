<?php

namespace App\Twig\Components\Form;

use App\Entity\Product;
use App\Entity\Label;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use App\Form\linkLabelToProductForm;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

#[AsLiveComponent]
class addLabelToProductForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public function __construct() {}

    #[LiveProp(writable: true)]
    public ?Product $product = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(linkLabelToProductForm::class);
    }

    #[LiveAction]
    public function saveLabel(EntityManagerInterface $entityManager): ?Response
    {
        $this->submitForm();

        if (!$this->getForm()->isValid()) {
            $this->addFlash('error', 'Il y a eu des erreurs dans la soumission du formulaire.');
            return null;
        }

        $form = $this->getForm();
        $isExisting = $form->get('isExisting')->getData();
        $selectedLabel = null;

        if ($isExisting) {
            $selectedLabel = $form->get('labelChoice')->getData();

            if (!$selectedLabel instanceof Label) {
                $this->addFlash('error', 'Aucune étiquette existante n\'a été sélectionnée. Veuillez choisir une étiquette dans la liste.');
                return null;
            }
        } else {
            $selectedLabel = $form->get('label')->getData();

            if (!$selectedLabel instanceof Label) {
                $this->addFlash('error', 'Impossible de créer une nouvelle étiquette. Les données sont manquantes ou invalides.');
                return null;
            }

            $entityManager->persist($selectedLabel);
        }

        if ($selectedLabel && $this->product) {
            $this->product->addLabel($selectedLabel);
            $entityManager->persist($this->product);
            $entityManager->flush();

            $this->addFlash('success', 'Étiquette liée au produit avec succès !');
            return $this->redirectToRoute('product_index');
        } else {
            $this->addFlash('error', 'Échec de la liaison de l\'étiquette au produit. Vérifiez que le produit et l\'étiquette sont corrects.');
            return null;
        }
    }
}