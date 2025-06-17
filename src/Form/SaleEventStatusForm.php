<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaleEventStatusForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('status', ChoiceType::class, [
            'choices' => [
                'Voir les événements passés' => 'passed',
                "Voir les événements du jour" => 'today',
                'Voir les événements à venir' => 'incoming',
            ],
            'placeholder' => 'Créer un événement',
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
