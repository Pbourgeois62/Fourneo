<?php

namespace App\Form;

use App\Entity\Unit;
use App\Entity\Recipe;
use App\Entity\StepProduct;
use Symfony\Component\Form\AbstractType;
use App\Form\AutoCompleteProductOnlyForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class StepProductForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $recipe = $options['recipe'] ?? null;

        $builder
            ->add('product', AutoCompleteProductOnlyForm::class, [
                'label' => 'Ingrédient',
                'attr' => [
                    'class' => 'w-full text-coffee',
                    'data-action' => 'live#action->prevent:change',
                ],
                'label_attr' => [
                    'class' => 'block text-sm font-medium text-coffee',
                ],
                'placeholder' => 'Sélectionnez un ingrédient',
                'required' => true,                
            ])
            ->add('quantity', NumberType::class, [
                'label' => 'Quantité utilisée à cette étape',
                'html5' => true,
                'scale' => 2,
                'attr' => [
                    'min' => 1,
                    'step' => 1,
                    'placeholder' => 'Quantité',
                    'data-action' => 'live#action->prevent:change',
                ],
                'required' => true,
            ])
             ->add('unit', EntityType::class, [
            'class' => Unit::class,
            'choice_label' => 'name', 
            'placeholder' => 'Sélectionnez une unité',
            'required' => false,
            'autocomplete' => true,
            'attr' => ['class' => 'w-full text-coffee'],
            'label_attr' => ['class' => 'block text-sm font-medium text-coffee']
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StepProduct::class,
            'recipe' => null,
        ]);
        $resolver->setAllowedTypes('recipe', [Recipe::class, 'null']);
    }
}