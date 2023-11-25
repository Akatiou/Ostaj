<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomProduit', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => ['placeholder' => 'Tapez le nom du produit']
            ])
            ->add('descriptionProduit', TextareaType::class, [
                'label' => 'Description courte',
                'attr' => ['placeholder' => 'Tapez une description courte du produit']
            ]);
        // ->add('image', UrlType::class, [
        //     'label' => 'Image du produit',
        //     'attr' => ['placeholder' => 'Tapez une URL d\'image !']
        // ])
        // ->add('prix', MoneyType::class, [
        //     'label' => 'Prix du produit',
        //     'attr' => ['placeholder' => 'Tapez le prix du produit en €'],
        //     'divisor' => 100,
        //     'required' => false
        // ])
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
};
