<?php

namespace App\Form;

use App\Entity\Product;
use App\Form\AutoCompleteAllergensForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddAllergensToProductForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('allergens', AutoCompleteAllergensForm::class, [
                'label' => 'Sélectionner des allergènes',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class, // Très important pour le mappage Many-to-Many automatique
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'link_allergen_with_product_form_submission',
        ]);
    }
}