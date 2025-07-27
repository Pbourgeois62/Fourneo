<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductCategoryForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('categoryName', ChoiceType::class, [
            'choices' => [
                'Voir tous les produits' => 'tous',
                'Voir les ingrédients' => 'ingrédient',
                "Voir les sous-recettes" => 'sous-recette',
                'Voir les produits déstinés à la vente' => 'a-vendre',
            ],
            'placeholder' => 'Choisir une catégorie ...',
            'required' => false,
            'mapped' => true,
            'label' => false
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'csrf_protection' => true, 
            'csrf_field_name' => '_token', 
            'csrf_token_id'   => 'sale_event_status_form_submission',
        ]);
    }
}
