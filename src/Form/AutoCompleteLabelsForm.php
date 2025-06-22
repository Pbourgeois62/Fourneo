<?php

namespace App\Form;

use App\Entity\Label;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;
use App\Repository\LabelRepository;

#[AsEntityAutocompleteField]
class AutoCompleteLabelsForm extends AbstractType
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Label::class,
            'searchable_fields' => ['createdAt'], 
            'label' => 'Chercher une étiquette non liée', 
            'choice_label' => 'formattedCreatedAt', 
            'multiple' => true,
            'constraints' => [
                new Count(min: 1, minMessage: 'Veuillez sélectionner au moins une étiquette'),
            ], 
            
            'query_builder' => function (LabelRepository $er) { 
                return $er->createQueryBuilder('l') 
                    ->leftJoin('l.produit', 'p') 
                    ->andWhere('l.produit IS NULL')
                    ->orderBy('l.id', 'ASC');
            },
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}