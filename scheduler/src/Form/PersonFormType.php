<?php

namespace App\Form;

use App\constants;
use App\Entity\PersonEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Email', EmailType::class, [
                'label' => 'Email *'
            ])
            ->add('Password', PasswordType::class, [
                'label' => 'Password *'
            ])
            ->add('PasswordAgain', PasswordType::class, [
                'mapped' => false,
                'label' => 'Confirm password *'
            ])
            ->add('Login', TextType::class, [
                'label' => 'User Identifier (Name) *'
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => constants::USER_ROLES,
                'multiple' => true,
                'expanded' => true,
                'label' => 'Select User Roles'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PersonEntity::class,
        ]);
    }
}
