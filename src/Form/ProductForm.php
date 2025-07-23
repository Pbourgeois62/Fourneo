<?php

namespace App\Form;

use App\Entity\Unit;
use App\Entity\Product;
use App\Entity\Allergen;
use App\Entity\Category;
use App\Repository\AllergenRepository;
use Symfony\Component\Form\AbstractType;
use Symfonycasts\DynamicForms\DependentField;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Type;
use Symfonycasts\DynamicForms\DynamicFormBuilder;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

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
                    new Type([
                        'type' => 'float',
                        'message' => 'Le prix doit être un nombre valide.',
                    ]),
                    new Positive(['message' => 'Le prix doit être un nombre positif.']),
                ],
            ])
            ->add('priceUnit', EntityType::class, [
                'class' => Unit::class,
                'choice_label' => 'name',
                'required' => true,
                'autocomplete' => true,
                'label' => 'Unité de prix',
                'placeholder' => 'Choisissez l\'unité d\'achat',
                'help' => 'L\'unité pour laquelle ce prix est appliqué (Ex: le kilogramme pour la farine).',
            ])
            ->add('size', TextType::class, [
                'label' => 'Taille',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Facultatif ... Entrez la taille',
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
                'autocomplete' => true,
                'label' => 'Catégorie du produit',
                'placeholder' => 'facultatif ... Choisissez une catégorie',
                'help' => 'Sélectionnez la catégorie à laquelle ce produit appartient.',
            ])
            ->add('isNewCategory', CheckboxType::class, [
                'label' => 'Ou souhaitez-vous créer une nouvelle catégorie ?',
                'required' => false,
                'mapped' => false,
            ])
            ->add('allergens', EntityType::class, [
                'class' => Allergen::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
                'autocomplete' => true,
                'by_reference' => false,
                'label' => 'Allergènes',
                'placeholder' => 'Choisissez un/des allergènes',
                'help' => 'Sélectionnez les allergènes présents dans ce produit.',
                'query_builder' => function (AllergenRepository $er) {
                    return $er->createQueryBuilder('a')->orderBy('a.name', 'ASC');
                },
            ])
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
