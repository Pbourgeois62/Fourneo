<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\RecipeProduct;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class AutoCompleteRecipeProductForm extends AbstractType
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => RecipeProduct::class,
            'searchable_fields' => ['product.name'],
            'label' => 'Associer un ingrédient de la recette',
            'choice_label' => 'product.name',
            'multiple' => false,
            'recipe' => null,
        ]);

        $resolver->setAllowedTypes('recipe', [Recipe::class, 'null']);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}