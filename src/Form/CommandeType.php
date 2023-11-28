<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

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
            ->add('telephoneClient', TextType::class, [
                'label' => 'Numéro de téléphone',
                'attr' => [
                    'placeholder' => 'Entrer votre numéro',
                    'class' => 'form-control'
                ]
            ])
            ->add('detailCommande', CollectionType::class, [
                'entry_type' => ProduitQuantiteType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false,
                // ... autres options
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
            'produit' => null, // Définissez vos options personnalisées ici
        ]);
    }
}
