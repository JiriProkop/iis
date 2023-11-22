<?php

namespace App\Form;

use App\Entity\PersonEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Email')
            ->add('Password')
            ->add('Login')
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'ROLE_ADMIN' => 'ROLE_ADMIN',
                    'ROLE_GUARANTOR' => 'ROLE_GUARANTOR',
                    'ROLE_TEACHER' => 'ROLE_TEACHER',
                    'ROLE_SCHEDULER' => 'ROLE_SCHEDULER',
                    'ROLE_STUDENT' => 'ROLE_STUDENT',
                    'ROLE_ELSE' => 'ROLE_ELSE'
                ],
                'multiple' => true,
                'expanded' => true,
            ])
//            ->add('Classes')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PersonEntity::class,
        ]);
    }
}
