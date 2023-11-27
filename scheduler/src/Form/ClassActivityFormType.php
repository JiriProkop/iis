<?php

namespace App\Form;

use App\Entity\ClassActivityEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClassActivityFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Name', TextType::class, [
                'label' => 'Name of the activity *'
            ])
            ->add('Repetition', ChoiceType::class, [
                'choices'  => [
                    'ONE_TIME' => 'ONE_TIME',
                    'ALL' => 'ALL',
                    'EVEN' => 'EVEN',
                    'ODD' => 'ODD',
                    ],
                'label' => 'Repetition of the activity *'])
            ->add('Length', IntegerType::class, [
                'label' => 'Length of the activity (in hours) *'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ClassActivityEntity::class,
        ]);
    }
}
