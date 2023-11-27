<?php

namespace App\Form;

use App\constants;
use App\Entity\ScheduleWindowEntity;
use App\Repository\RoomEntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RepeatingScheduleFormType extends AbstractType
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
            ->add('Start', TimeType::class, [
                'input'  => 'timestamp',
                'widget' => 'choice',
                'mapped' => false,
                'label' => 'Select activity start time *'
            ])
            ->add('Day', ChoiceType::class, [
                'choices' => constants::DAY_STR_FORMAT_INTERVALS,
                'mapped' => false,
                'label' => 'Select activity day *'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ScheduleWindowEntity::class,
        ]);
    }
}
