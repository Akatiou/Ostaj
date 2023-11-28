<?php

namespace App\Form;

use App\Entity\DetailCommande;
use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ProduitQuantiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('produit', EntityType::class, [
                'class' => Produit::class,
                'choice_label' => 'nomProduit',
                'attr' => [
                    'placeholder' => 'Sélectionner un produit',
                ],
                // ... autres options pour le champ produit
            ])
            ->add('quantite', IntegerType::class, [
                'label' => 'Quantité',
                'attr' => [
                    'min' => 0,
                    'placeholder' => 'Indiquez le nombre désiré'
                ],
                // ... autres options pour le champ quantite
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Définir la classe associée à cette collection
            'data_class' => DetailCommande::class, // Remplacez par la classe de votre collection
        ]);
    }
}
