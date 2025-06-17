<?php

namespace App\Form;

use App\Entity\Allergen;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AllergenForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l’allergène',
                'attr' => [
                    'placeholder' => 'Ex: Gluten',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description (facultative)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: provoque des douleurs ou des maux de gorge',
                    'rows' => 3,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Allergen::class,
        ]);
    }
}
