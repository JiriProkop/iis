<?php

namespace App\Form;

use App\Entity\ScheduleWindowEntity;
use App\Repository\RoomEntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OneTimeScheduleFormType extends AbstractType
{
    private RoomEntityRepository $roomRepository;
    public function __construct(RoomEntityRepository $roomRepository) {
        $this->roomRepository = $roomRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $rooms = $this->roomRepository->findAll();
        $rooms_names = array();
        foreach ($rooms as $room) {
            $rooms_names[$room->getName()] = $room->getId();
        }

        $builder
            ->add('Rooms', ChoiceType::class, [
                'multiple' => true,
                'choices'  => $rooms_names,
                'mapped' => false,
                'label' => 'Room with the activity *'
            ])
            ->add('Start', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Select the activity date and time *'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ScheduleWindowEntity::class,
        ]);
    }
}
