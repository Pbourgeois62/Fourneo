<?php

namespace App\Form;

use App\Entity\Allergen;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class AutoCompleteAllergens extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Allergen::class,
            'searchable_fields' => ['name'],
            'label' => 'Ajouter des allergènes',
            'choice_label' => 'name',
            'multiple' => true,
            'required' => false,                        
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
