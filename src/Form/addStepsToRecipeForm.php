<?php

namespace App\Form;

use App\Entity\Unit;
use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class addStepsToRecipeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $recipeEntity = $options['data'];

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la recette',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de la recette',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Décris ici ta recette',
                    'class' => 'form-textarea',
                ],
            ])
            ->add('yield', NumberType::class, [
                'label' => 'Rendement de la recette',
                'empty_data' => 1.0,
                'scale' => 2,
                'constraints' => [
                    new NotBlank(['message' => 'Le rendement est obligatoire.']),
                ],
            ])
            ->add('yieldUnit', EntityType::class, [
                'class' => Unit::class,
                'autocomplete' => true,
                'label' => 'Unité de rendement',
                'attr' => [
                    'placeholder' => 'Choisir une unité',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'L\'unité de rendement est obligatoire.']),
                ],
                'choice_label' => 'name',
            ])
            ->add('isSubRecipe', CheckboxType::class, [
                'label' => 'Peut être utilisée comme sous-recette',
                'required' => false,
                'mapped' => true,
                'label_attr' => ['class' => 'block text-sm font-medium text-coffee mt-4'],
            ])
            ->add('steps', LiveCollectionType::class, [
                'entry_type' => RecipeStepForm::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'entry_options' => [
                    'recipe' => $recipeEntity,
                ],
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'add_steps_to_recipe_item',
        ]);
    }
}
