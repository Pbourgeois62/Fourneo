<?php

namespace App\Twig\Components\Form;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductForm;
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

#[AsLiveComponent]
class registerIngredientForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use LiveCollectionTrait;

    public function __construct() {}

    #[LiveProp(writable: true)]
    public ?Product $product = null;     
  
    protected function instantiateForm(): FormInterface
    {       
        return $this->createForm(ProductForm::class, 
        $this->product
    );
    }

    #[LiveAction]
    public function save(EntityManagerInterface $entityManager, CategoryRepository $categoryRepository)
    {
        $this->submitForm();

        if (!$this->getForm()->isValid()) {
            $this->addFlash('error', 'Erreurs de validation, veuillez corriger les champs.');
            return;
        }
        $product = $this->getForm()->getData();
        $ingredientCategory = $categoryRepository->findOneBy(['name' => 'ingrédient']);
        if ($ingredientCategory === null) {
            $ingredientCategory = new Category();
            $ingredientCategory->setName('ingrédient');
            $entityManager->persist($ingredientCategory);
        } else {
            $product->addCategory($ingredientCategory);
        }        
        $entityManager->persist($product);
        $entityManager->flush();

        $this->addFlash('success', 'ingrédient enregistré avec succès !');

        return $this->redirectToRoute('product_index');
    }
}
