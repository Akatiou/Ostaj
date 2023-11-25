<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomClient', TextType::class, [
                'label' => 'Nom et prénom',
                'attr' => [
                    'placeholder' => 'Tapez votre nom et prénom',
                    'class' => 'form-control'
                ]
            ])
            ->add('emailClient', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'Entrer votre adresse email',
                    'class' => 'form-control'
                ]
            ])
            ->add('telephoneClient', NumberType::class, [
                'label' => 'Numéro de téléphone',
                'attr' => [
                    'placeholder' => 'Entrer votre numéro',
                    'class' => 'form-control'
                ]
            ]);

        // Récupérer tous les produits depuis la base de données
        $produit = $options['produit'];

        foreach ($produit as $produit) {
            $builder
                ->add('produit_' . $produit->getId(), EntityType::class, [
                    'class' => Produit::class,
                    'choice_label' => 'nomProduit',
                    'label' => $produit->getNomProduit(),
                    'choice_value' => 'id',
                    'data' => $produit,
                    'mapped' => false
                    // ... autres options pour le champ produit
                ])
                ->add('quantite' . $produit->getId(), IntegerType::class, [
                    'label' => 'Quantité pour ' . $produit->getNomProduit(),
                    'attr' => ['min' => 0],
                    'mapped' => false
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
            'produit' => null,
        ]);
    }
}
