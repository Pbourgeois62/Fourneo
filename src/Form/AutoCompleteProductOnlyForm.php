<?php

namespace App\Form;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField()]
class AutoCompleteProductOnlyForm extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Product::class,
            'searchable_fields' => ['name'],
            'placeholder' => 'Rechercher un produit...',
            'choice_label' => 'displayName',
            'query_builder' => function (ProductRepository $productRepository) {
                return $productRepository->createQueryBuilderForSubRecipeOrIngredientProducts();
            },
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}