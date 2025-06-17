<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfonycasts\DynamicForms\DependentField;
use Symfonycasts\DynamicForms\DynamicFormBuilder;

class SaleEventStatusForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $dynamicBuilder = new DynamicFormBuilder($builder);

        $dynamicBuilder->add('status', ChoiceType::class, [
            'choices' => [
                'Passé' => 'passed',
                "Aujourd'hui" => 'today',
                'À venir' => 'incoming',
            ],
            'placeholder' => 'Choisissez un statut',
            'required' => false,
            'mapped' => true,
        ]);

        $dynamicBuilder->addDependent('status', 'chosen', function (DependentField $field, ?string $status) {
            if (null === $status) {
                return;
            }

            $field->add(SaleEventAutocompeteField::class, [
                'label' => 'Produits associés',
                'required' => false,                
                'status' => $status,
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
