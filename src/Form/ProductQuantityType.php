<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\ProductEvent; // <--- Make sure this is imported!
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver; // <--- Make sure this is imported!

class ProductQuantityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisir un produit',
                'label' => 'Produit', // Added label for clarity
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité', // Added label for clarity
                'attr' => ['min' => 1, 'placeholder' => 'Quantité'], // Added placeholder
            ]);
    }

    // --- Add this method ---
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductEvent::class, // <--- **This is the crucial line!**
        ]);
    }
    // --- End of added method ---
}