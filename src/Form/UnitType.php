<?php

// src/Form/UnitType.php

namespace App\Form;

use App\Entity\Unit;
use App\Repository\UnitRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class UnitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'unité',
                'attr' => [
                    'placeholder' => 'Ex: gramme, litre, pièce',
                    'class' => 'w-full text-coffee'
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-coffee']
            ])
            ->add('factorToBase', NumberType::class, [
                'label' => 'Facteur de conversion (vers l\'unité de base)',
                'html5' => true,
                'scale' => 6,
                'attr' => [
                    'placeholder' => 'Ex: 0.001 pour gramme vers kilogramme, 1.0 pour kilogramme',
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500',
                    'min' => 0.000001,
                    'step' => 0.000001
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-coffee'],
                'help' => 'Ex: Pour "gramme" si "kilogramme" est sa base, entrez 0.001 (car 1g = 0.001kg). Pour "kilogramme", entrez 1.0.'
            ])
            ->add('baseUnit', EntityType::class, [
                'class' => Unit::class,
                'choice_label' => 'name',
                'label' => 'Sélectionnez l\'unité de base (laissez vide si c\'est une unité de base principale)',
                'placeholder' => 'Choisir une unité de base',
                'required' => false,
                'autocomplete' => true,
                'attr' => ['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500'],
                'label_attr' => ['class' => 'block text-sm font-medium text-coffee'],
                'query_builder' => function (UnitRepository $er) use ($options) {
                    $qb = $er->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');

                    $unit = $options['data'];
                    if ($unit && $unit->getId()) {
                        $qb->andWhere('u.id != :currentUnitId')
                           ->setParameter('currentUnitId', $unit->getId());
                    }
                    return $qb;
                },
                'help' => 'C\'est l\'unité "parente" vers laquelle cette unité se convertit (ex: "litre" pour "millilitre").'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Unit::class,
        ]);
    }
}