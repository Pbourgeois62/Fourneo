<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;
use Doctrine\ORM\EntityRepository;

#[AsEntityAutocompleteField]
class AutoCompleteProductOnlyForm extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Product::class,
            'searchable_fields' => ['name'],
            'label' => 'Rechercher un produit',
            'choice_label' => 'name',
            'multiple' => false,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('p')
                          ->orderBy('p.name', 'ASC');
            },
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}