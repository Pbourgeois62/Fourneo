<?php

namespace App\Form;

use App\Entity\Label;
use DateTimeImmutable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class LabelTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder          

            ->add('imageFile', VichImageType::class, [
                'label'        => 'Photo de l\'étiquette',
                'required'     => false,
                'allow_delete' => false,
                'download_uri' => false,
                'image_uri'    => false,
            ])           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Label::class,
        ]);
    }
}
