<?php

namespace App\Twig\Components\Form;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductTypeForm;
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
class registerProductForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use LiveCollectionTrait;

    public function __construct() {}

    #[LiveProp(writable: true)]
    public ?Product $product = null;     
  
    protected function instantiateForm(): FormInterface
    {       
        return $this->createForm(ProductTypeForm::class, 
        $this->product
    );
    }

    #[LiveAction]
    public function save(EntityManagerInterface $entityManager)
    {
        $this->submitForm();

        if (!$this->getForm()->isValid()) {
            $this->addFlash('error', 'Erreurs de validation, veuillez corriger les champs.');
            return;
        }
        $product = $this->getForm()->getData();
        $newCategoryName = $this->getForm()->get('newCategory')->getData();
        if ($newCategoryName) {
            $newCategory = new Category();
            $newCategory->setName($newCategoryName);
            $product->setCategory($newCategory);
        }
        $entityManager->persist($newCategory);
        $entityManager->persist($product);
        $entityManager->flush();

        $this->addFlash('success', 'Produit enregistré avec succès !');

        return $this->redirectToRoute('product_index');
    }
}
