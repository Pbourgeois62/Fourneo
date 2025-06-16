<?php

namespace App\Form;

use App\Entity\ProductEvent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ProductEventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $productEvent = $event->getData(); // Get the ProductEvent object
                $form = $event->getForm();

                // Check if $productEvent is an instance of ProductEvent and not null
                // This is important because $productEvent could be null for new entities
                if ($productEvent instanceof ProductEvent && null !== $productEvent->getQuantity()) {
                    $maxQuantity = $productEvent->getQuantity();
                } else {
                    // Default max value if productEvent is not set or quantity is null
                    $maxQuantity = 9999; // Or any other suitable default
                }

                $form->add('unsoldQuantity', IntegerType::class, [
                    'label' => false,
                    'attr' => [
                        'min' => 0,
                        'max' => $maxQuantity, // Set max dynamically
                        'class' => 'w-24 px-2 py-1 border rounded-md text-center'
                    ],
                ]);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductEvent::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'product_event_form_submission',
        ]);
    }
}