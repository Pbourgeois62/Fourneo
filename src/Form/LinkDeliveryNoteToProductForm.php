<?php

namespace App\Form;

use App\Entity\DeliveryNote;
use Symfony\Component\Form\AbstractType;
use App\Repository\DeliveryNoteRepository;
use Symfonycasts\DynamicForms\DependentField;
use Symfony\Component\Form\FormBuilderInterface;
use Symfonycasts\DynamicForms\DynamicFormBuilder;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class LinkDeliveryNoteToProductForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder = new DynamicFormBuilder($builder);

        $builder
            ->add('DeliveryNote', DeliveryNoteTypeForm::class, [])
            ->add('isExisting', CheckboxType::class, [
                'label' => 'Souhaitez-vous lier un bon de commande existant ?',
            ]);

        $builder->addDependent('deliveryNoteChoice', 'isExisting', function (DependentField $field, ?bool $isExisting) {
            if (!$isExisting) {
                return;
            }

            $field->add(EntityType::class, [
                'class' => DeliveryNote::class,
                'query_builder' => function (DeliveryNoteRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->leftJoin('d.products', 'p')
                        ->andWhere('p.id IS NULL')
                        ->orderBy('d.number', 'ASC');
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
                'choice_label' => 'number',
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