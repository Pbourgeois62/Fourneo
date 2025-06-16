<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use App\Form\ProductEventFormType; // Assurez-vous d'importer votre ProductEventType

class test extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('productEvents', CollectionType::class, [
                'entry_type' => ProductEventFormType::class, // Le type de formulaire pour chaque élément de la collection
                'entry_options' => ['label' => false], // Cache le label de chaque sous-formulaire
                'allow_add' => false, // Vous ne voulez probablement pas ajouter de nouveaux ProductEvent ici
                'allow_delete' => false, // Ni en supprimer
                'by_reference' => false, // Très important pour Doctrine si vous modifiez la collection via des setters
                // 'label' => 'Invendus par produit', // Label du champ collection, si vous le souhaitez
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // 'data_class' => SaleEvent::class, // Optionnel: Si ce formulaire gère directement une entité SaleEvent
            // Si vous ne le liez pas directement à une SaleEvent, vous le traitez comme un tableau de ProductEvent
            'data_class' => null, // Ou un tableau vide si vous ne liez pas à une entité parente
        ]);
    }
}