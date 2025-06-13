<?php

namespace App\Form;

use App\Entity\SaleEvent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

class SaleEventTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Entrer un nom pour votre événement ...']
            ])
            ->add('description', TextType::class, [
                'label' => 'Description (facultatif)',
                'required' => false,
                'attr' => ['placeholder' => 'Entrer une description pour votre événement ...']
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse (facultatif)',
                'required' => false,
                'attr' => ['placeholder' => 'Entree le lieu de votre événement ...']
            ])
            ->add('startDate', DateTimeType::class, [
                'label' => 'Début',
                'input' => 'datetime_immutable',
                'attr' => ['placeholder' => 'Entrer la date et l\'heure de début de votre événement ...']
            ])
            ->add('endDate', DateTimeType::class, [
                'label' => 'Fin',
                'input' => 'datetime_immutable',
                'attr' => ['placeholder' => 'Entrer la date et l\'heure de fin de votre événement ...']
            ])
            ->add('productEvents', LiveCollectionType::class, [
                'entry_type' => ProductQuantityType::class,
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
            'data_class' => SaleEvent::class,
            'csrf_protection' => true, 
            'csrf_field_name' => '_token', 
            'csrf_token_id'   => 'sale_event_form_submission',
        ]);
    }
}
