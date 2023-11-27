<?php

namespace App\Form;

use App\constants;
use App\Entity\ScheduleWindowEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RepeatingScheduleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Start', TimeType::class, [
                'input'  => 'timestamp',
                'widget' => 'choice',
                'mapped' => false
            ])
            ->add('Day', ChoiceType::class, [
                'choices' => constants::DAY_STR_FORMAT_INTERVALS,
                'mapped' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ScheduleWindowEntity::class,
        ]);
    }
}
