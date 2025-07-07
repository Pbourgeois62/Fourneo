<?php

namespace App\Form;

use App\Entity\RecipeProduct;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\AutoCompleteproductForm;

class RecipeProductForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // This is where you use your Autocomplete form for the 'product' part of RecipeProduct
            ->add('product', AutoCompleteproductForm::class, [
                'label' => 'Ingrédient',                
                'attr' => [
                    'data-action' => 'live#action->prevent:change', // Important for LiveComponents if you want to react on selection
                    // You might need to add specific data-attributes for your LiveComponent interaction
                    // depending on how you structure the add/remove actions.
                ],
            ])
            ->add('quantity', NumberType::class, [
                'label' => 'Quantité',
                'html5' => true,
                'attr' => [
                    'min' => 0.01,
                    'step' => 0.01,
                    'placeholder' => 'Quantité',
                    'data-action' => 'live#action->prevent:change', // LiveComponent will react to changes
                ],
                'required' => true,
            ])
            ->add('unit', TextType::class, [ // Assuming 'unit' is a string. If it's an entity, use EntityType.
                'label' => 'Unité',
                'attr' => [
                    'placeholder' => 'Ex: g, kg, ml, L, unités...',
                    'data-action' => 'live#action->prevent:change', // LiveComponent will react to changes
                ],
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecipeProduct::class, // This form maps to a single RecipeProduct entity
        ]);
    }
}