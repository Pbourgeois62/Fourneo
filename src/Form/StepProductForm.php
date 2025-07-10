<?php

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use App\Entity\StepProduct;
use App\Form\AutoCompleteRecipeProductForm;

class StepProductForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $recipeProductsChoices = $options['recipe_products_choices'];
        $recipeGlobal = $options['recipe_global'] ?? null;

        $builder
            ->add('recipeProduct', AutoCompleteRecipeProductForm::class, [ 
                'label' => 'Ingrédient de la recette',
                'choices' => $recipeProductsChoices,
                'recipe' => $recipeGlobal,
                'attr' => ['class' => 'w-full text-coffee'],
                'label_attr' => ['class' => 'block text-sm font-medium text-coffee'],
            ])
            ->add('quantity', NumberType::class, [
                'label' => 'Quantité utilisée',
                'html5' => true,
                'required' => false,
                'attr' => ['class' => 'w-full text-center text-coffee'],
                'label_attr' => ['class' => 'block text-sm font-medium text-coffee'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'recipe_global' => null,
            'data_class' => StepProduct::class,
            'recipe' => null,
        ]);
        $resolver->setRequired('recipe_products_choices');
        $resolver->setAllowedTypes('recipe_products_choices', 'array');
        $resolver->setDefined('recipe_global');
        $resolver->setAllowedTypes('recipe_global', [Recipe::class, 'null']);
    }
}