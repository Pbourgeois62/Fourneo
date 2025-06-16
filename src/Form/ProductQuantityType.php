<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\ProductEvent; 
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver; 

class ProductQuantityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisir un produit',
                'label' => 'Produit', 
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité', 
                'attr' => ['min' => 1, 'placeholder' => 'Quantité'], 
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductEvent::class, 
        ]);
    }
}