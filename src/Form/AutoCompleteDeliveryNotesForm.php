<?php

namespace App\Form;

use App\Entity\DeliveryNote;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;
use App\Repository\DeliveryNoteRepository; // Importez DeliveryNoteRepository ici

#[AsEntityAutocompleteField]
class AutoCompleteDeliveryNotesForm extends AbstractType
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => DeliveryNote::class,         
            'searchable_fields' => ['number', 'name'], 
            'label' => 'Chercher un bon de livraison non lié',
            'choice_label' => 'number',
            'multiple' => true,
            'constraints' => [
                new Count(min: 1, minMessage: 'Veuillez sélectionner au moins un bon de livraison'),
            ], 
            
            'query_builder' => function (DeliveryNoteRepository $er) { // Changez QueryBuilder $qb en DeliveryNoteRepository $er
                return $er->createQueryBuilder('dn') // Appelez createQueryBuilder sur l'instance du repository
                    ->leftJoin('dn.products', 'p') 
                    ->andWhere('p.id IS NULL')
                    ->orderBy('dn.number', 'ASC'); 
            },
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}