<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfonycasts\DynamicForms\DynamicFormBuilder;
use Symfonycasts\DynamicForms\DependentField;
use Symfony\Component\Validator\Constraints\Type;

class ProductForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder = new DynamicFormBuilder($builder);

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Entrez le nom du produit (ex: Croissant pur beurre)',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le nom du produit est obligatoire.']),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le nom du produit doit contenir au moins {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix',
                'required' => true,
                'currency' => 'EUR',
                'attr' => [
                    'placeholder' => 'Entrez le prix (ex: 2.34)',
                ],
                'constraints' => [
                    new NotNull(['message' => 'Le prix est obligatoire.']),
                    new Type([
                        'type' => 'float',
                        'message' => 'Le prix doit être un nombre valide.',
                    ]),
                    new Positive(['message' => 'Le prix doit être un nombre positif.']),
                ],
            ])
            ->add('size', TextType::class, [
                'label' => 'Taille',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Entrez la taille',
                ],
                'constraints' => [
                    new Length([
                        'max' => 10,
                        'maxMessage' => 'La taille ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'required' => false,
                'label' => 'Catégorie du produit',
                'placeholder' => 'Choisissez une catégorie',
                // 'expanded' => true,
                'help' => 'Sélectionnez la catégorie à laquelle ce produit appartient.',
            ])
            ->add('isNewCategory', CheckboxType::class, [
                'label' => 'Ou souhaitez-vous créer une nouvelle catégorie ?',
                'required' => false,
                'mapped' => false,
            ])
            ->add('allergens', AutoCompleteAllergens::class)
        ;

        $builder->addDependent('newCategory', 'isNewCategory', function (DependentField $field, ?bool $isNewCategory) {
            if (!$isNewCategory) {
                return;
            }

            $field->add(TextType::class, [
                'label' => 'Nouvelle catégorie',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Entrez le nom de la nouvelle catégorie',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le nom de la nouvelle catégorie est requis.']),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'La nouvelle catégorie doit contenir au moins {{ limit }} caractères.',
                    ]),
                ],
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'product_form_submission',
        ]);
    }
}
