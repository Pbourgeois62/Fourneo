<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use App\Form\AutoCompleteDeliveryNotesForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddDeliveryNotesToProductForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void    {

        $builder
            ->add('deliveryNotes', AutoCompleteDeliveryNotesForm::class)      
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'link_delivery_note_with_produt_form_submission',
        ]);
    }
}