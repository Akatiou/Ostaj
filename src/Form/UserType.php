<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => 'Nom et prénom',
                'attr' => ['placeholder' => 'Tapez le nom et le prénom...']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse Email',
                'attr' => ['placeholder' => 'Entrez l\'adresse mail...']
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'attr' => ['placeholder' => 'Tapez un mot de passe...']
            ]);
        // ->add('roles', ChoiceType::class, [
        //     'label' => 'Rôles',
        //     'choices' => [
        //         'ROLE_USER' => 'ROLE_USER',
        //         'ROLE_ADMIN' => 'ROLE_ADMIN',
        //     ],
        //     'multiple' => false
        // ]);
        // ->add('roles');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
