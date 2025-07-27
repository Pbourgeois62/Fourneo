<?php

namespace App\Twig\Components\Form;

use App\Form\ProductCategoryForm;
use App\Repository\ProductRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent]
class filterProductsByCategory extends AbstractController
{   
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp(writable: true)]
    public ?string $categoryName = null;

    public array $products = [];

    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    protected function instantiateForm(): FormInterface
    {
         return $this->createForm(ProductCategoryForm::class, null, [
            'data' => ['categoryName' => $this->categoryName]
        ]);
    }

    public function mount(): void
    {
        $this->products = $this->productRepository->findAll();
         $this->categoryName = $this->getForm()->get('categoryName')->getData();
    }

    #[LiveAction]
    public function filterProductsBycategory(): void
    {
        $this->submitForm();
        $categoryName = $this->getForm()->get('categoryName')->getData();
        if ($categoryName === 'tous' || $categoryName === null) {
            $this->products = $this->productRepository->findAll();
        } else {
            $this->products = $this->productRepository->findByCategoryName($categoryName);
        }       
    }
}
