<?php

namespace App\Form;

use App\Entity\ClassEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClassFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Abbreviation', TextType::class, [
                'label' => 'Abbreviation *'
            ])
            ->add('Name', TextType::class, [
                'label' => 'Name of the class *'
            ])
            ->add('Anotation', TextType::class, [
                'label' => 'Annotation',
                'required' => false
            ])
            ->add('Credits', IntegerType::class, [
                'label' => 'Credits *'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ClassEntity::class,
        ]);
    }
}
