<?php

namespace App\Form;

use App\Entity\RecipeProduct;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\AutoCompleteProductOnlyForm; // <-- Importez le nouveau formulaire ici !

class RecipeProductForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('product', AutoCompleteProductOnlyForm::class, [
                'label' => 'Produit',
                'attr' => [
                    'data-action' => 'live#action->prevent:change',
                ],
            ])
            ->add('quantity', NumberType::class, [
                'label' => 'Quantité',
                'html5' => true,
                'attr' => [
                    'min' => 0.01,
                    'step' => 0.01,
                    'placeholder' => 'Quantité',
                    'data-action' => 'live#action->prevent:change',
                ],
                'required' => true,
            ])
            ->add('unit', TextType::class, [
                'label' => 'Unité',
                'attr' => [
                    'placeholder' => 'Ex: g, kg, ml, L, unités...',
                    'data-action' => 'live#action->prevent:change',
                ],
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecipeProduct::class,
        ]);
    }
}