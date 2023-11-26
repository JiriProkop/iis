<?php

namespace App\Form;

use App\constants;
use App\Entity\PersonalActivityEntity;
use App\Entity\ScheduleWindowEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonalActivityFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Description',TextareaType::class, ['required' => false ])
            ->add('Room', TextType::class, [
                'required' => false,
                'mapped' => false
            ])
            ->add('Date', DateType::class, [
                'widget' => 'choice',
                'mapped' => false
            ])
            ->add('Time_from', TimeType::class, [
                'widget' => 'choice',
                'with_minutes' => false,
                'placeholder' => [
                    'hour' => 'Hour',
                ],
                'mapped' => false
            ])
            ->add('Time_to', TimeType::class, [
                'widget' => 'choice',
                'with_minutes' => false,
                'placeholder' => [
                    'hour' => 'Hour',
                ],
                'mapped' => false
            ])
            ->add('Repetition',ChoiceType::class, [
                'choices' => constants::REPETITIONS,
                'multiple' => false,
                'expanded' => false,
            ])
//            ->add('Length', IntegerType::class, ['required' => false ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PersonalActivityEntity::class,
        ]);
    }
}