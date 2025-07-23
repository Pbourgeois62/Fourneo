<?php

namespace App\Form;

use App\Entity\Step;
use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class RecipeStepForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $recipe = $options['recipe'];

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'étape',
                'required' => false,
            ])
            ->add('description', TextType::class, [
                'label' => 'Description de l\'étape',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Décris ici l\'étape',
                    'class' => 'form-textarea',
                ],
            ])
            ->add('durationMinutes', IntegerType::class, [
                'label' => 'Durée (minutes)',
                'required' => false,
            ])            
            ->add('stepProducts', LiveCollectionType::class, [
                'entry_type' => StepProductForm::class,
                'entry_options' => [
                    'label' => false,
                    'recipe' => $recipe,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Step::class,
        ]);
        $resolver->setRequired('recipe');
        $resolver->setAllowedTypes('recipe', Recipe::class);
    }
}