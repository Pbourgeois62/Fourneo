<?php

namespace App\Form;

use App\Entity\label;
use Symfony\Component\Form\AbstractType;
use App\Repository\labelRepository;
use Symfonycasts\DynamicForms\DependentField;
use Symfony\Component\Form\FormBuilderInterface;
use Symfonycasts\DynamicForms\DynamicFormBuilder;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class linkLabelToProductForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder = new DynamicFormBuilder($builder);

        $builder
            ->add('label', LabelTypeForm::class, [])
            ->add('isExisting', CheckboxType::class, [
                'label' => 'Souhaitez-vous lier une étiquette existante ?',
            ]);

        $builder->addDependent('labelChoice', 'isExisting', function (DependentField $field, ?bool $isExisting) {
            if (!$isExisting) {
                return;
            }

            $field->add(EntityType::class, [
                'class' => Label::class,
                'query_builder' => function (LabelRepository $er) {
                    return $er->createQueryBuilder('l')
                        ->leftJoin('l.produit', 'p')
                        ->andWhere('l.produit IS NULL')
                        ->orderBy('l.id', 'ASC');
                },
                'autocomplete' => true,
                'required' => false,
                'mapped' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Entrez le numéro de bon de commande',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le numéro de bon de commande est requis.']),                    
                ],
                'choice_label' => 'formattedCreatedAt',
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'link_delivery_note_with_produt_form_submission',
        ]);
    }
}