<?php

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

class addStepsToRecipeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder                                
           
            ->add('steps', LiveCollectionType::class, [
                'entry_type' => RecipeStepForm::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'entry_options' => [
                    'recipe' => $options['data'],
                ],
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
