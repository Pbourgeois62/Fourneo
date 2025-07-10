<?php

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Form\RecipeProductForm;

class RecipeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la recette',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Ex: Pain au chocolat ...',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de la recette',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Décrivez la recette, son origine, ses particularités...',
                    'rows' => 5,
                ],
            ])
            ->add('yield', NumberType::class, [
                'label' => 'Rendement de la recette',
                'required' => false,
                'html5' => true,
                'attr' => [
                    'placeholder' => 'Ex: Une recette produira X petits pains au chocolat',
                    'min' => 0,
                ],
            ])
            ->add('recipeProducts', LiveCollectionType::class, [
                'entry_type' => RecipeProductForm::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'button_delete_options' => [
                    'label' => 'X',
                    'attr' => [
                        'class' => 'text-red-600 hover:text-red-800 p-0 focus:outline-none flex items-center justify-center w-8 h-8 rounded-full', // Ajoutez des classes pour la forme et le centrage
                        'title' => 'Supprimer ce produit',
                    ],
                    'row_attr' => [
                        'class' => 'flex items-center justify-between',
                    ],
                ],
            ])           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'add_recipe_form_submission',
        ]);
    }
}
