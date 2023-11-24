<?php

namespace App\Form;

use App\Entity\PersonalActivityEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonalActivityFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Description',TextType::class, ['required' => false ])
            ->add('Initial day', DateType::class)
            ->add('Activity from', TimeType::class, [
                'placeholder' => [
                    'hour' => 'hours',
                ],
            ])
            ->add('Activity to', TimeType::class, [
                'placeholder' => [
                    'hour' => 'hours',
                ],
            ])
            ->add('Repetition',TextType::class, ['required' => false ])
            ->add('Length', IntegerType::class, ['required' => false ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PersonalActivityEntity::class,
        ]);
    }
}