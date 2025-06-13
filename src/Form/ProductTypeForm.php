<?php

namespace App\Form;

use App\Entity\Category; // Correct casing for Category entity
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;   // For price
use Symfony\Component\Form\Extension\Core\Type\TextType;    // For name, size
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [ // Explicitly define TextType
                'label' => 'Nom du produit',
                'attr' => [
                    'placeholder' => 'Entrez le nom du produit (ex: Croissant pur beurre)',
                ],
            ])
            ->add('price', MoneyType::class, [ // Use MoneyType for prices
                'label' => 'Prix',
                'currency' => 'EUR', // Set currency, e.g., Euro
                'attr' => [
                    'placeholder' => 'Entrez le prix (ex: 2.34)',
                ],
            ])
            ->add('size', TextType::class, [ // Explicitly define TextType
                'label' => 'Taille',
                'required' => false, // Assuming size might be optional
                'attr' => [
                    'placeholder' => 'Entrez la taille',
                ],
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class, // Use correct casing for Category
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true, // <--- THIS IS KEY for checkboxes!
                'required' => false,
                'label' => 'Catégories (Vous pouvez en séléctionner plusieurs)',
                'placeholder' => 'Choisissez une ou plusieurs catégories', // Placeholder for select (if not expanded)
                'help' => 'Sélectionnez les catégories auxquelles ce produit appartient.', // A helpful hint
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}