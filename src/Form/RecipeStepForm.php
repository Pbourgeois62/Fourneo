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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class RecipeStepForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $recipe = $options['recipe'];

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'étape',
                'attr' => [
                    'placeholder' => 'Ex: Préparation de la garniture ...',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de l\'étape',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: Dans un grand saladier, mélangez la farine, le sel et le sucre...',
                    'rows' => 5,
                ],
            ])
            ->add('number', IntegerType::class, [
                'label' => 'Ordre de l\'étape',          
            ])
           
            ->add('stepProducts', LiveCollectionType::class, [
                'entry_type' => StepProductForm::class,
                'entry_options' => [
                    'label' => false, 
                    'recipe' => $recipe,
                    'recipe_products_choices' => $recipe ? $recipe->getRecipeProducts()->toArray() : [],
                ],
                'button_delete_options' => [
                    'label' => 'X',
                    'attr' => [
                        'class' => 'text-red-600 hover:text-red-800 p-0 focus:outline-none flex items-center justify-center w-8 h-8 rounded-full',
                        'title' => 'Supprimer ce produit',
                    ],
                    'row_attr' => [
                        'class' => 'flex items-center justify-between',
                    ],
                ],
                'allow_add' => true,     
                'allow_delete' => true,   
                'by_reference' => false,                
                'label' => false,        
            ])
            ;
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