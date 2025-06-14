<?php
namespace App\Form;

use App\Entity\ProductEvent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType; // Pour la quantité
use Symfony\Component\Form\Extension\Core\Type\HiddenType; // Pour l'ID si nécessaire

class ProductEventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Le champ 'unsoldQuantity' qui sera modifiable
            ->add('unsoldQuantity', IntegerType::class, [
                'label' => false, // Souvent pas besoin de label pour chaque champ dans une collection
                'attr' => [
                    'min' => 0,
                    'class' => 'w-24 px-2 py-1 border rounded-md text-center' // Style Tailwind
                ],
                // Optionnel: ajouter des contraintes de validation ici ou via les annotations de l'entité
            ])
            // Vous pouvez ajouter d'autres champs si nécessaire, par exemple l'ID du ProductEvent en caché
            // ->add('id', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductEvent::class,
            // Permet de ne pas avoir à relier les champs aux propriétés de l'entité si vous avez des champs qui ne correspondent pas directement
            // 'by_reference' => false,
        ]);
    }
}