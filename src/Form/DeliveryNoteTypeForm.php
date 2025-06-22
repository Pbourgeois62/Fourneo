<?php

namespace App\Form;

use DateTimeImmutable;
use App\Entity\DeliveryNote;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DeliveryNoteTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Numéro du BL
            ->add('number', TextType::class, [
                'label' => 'Numéro',
            ])

            // Image (gérée par VichUploader)
            ->add('imageFile', VichImageType::class, [
                'label'          => 'Bon de livraison (image)',
                'required'       => false,               
                'allow_delete'   => false,
                'download_uri'   => false,
                'image_uri'      => false,                
            ])
            ->add('registeredAt', DateType::class, [
                'data'            => new DateTimeImmutable(),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DeliveryNote::class,
        ]);
    }
}
