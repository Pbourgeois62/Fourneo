<?php

namespace App\Form;

use App\Entity\Product;
use App\Form\AutoCompleteLabelsForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddLabelToProductForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void    {

        $builder
            ->add('labels', AutoCompleteLabelsForm::class)      
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'link_label_with_produt_form_submission',
        ]);
    }
}