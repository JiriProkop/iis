<?php

namespace App\Entity;

use App\Repository\RoomActivityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomActivityRepository::class)]
class RoomActivity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'roomActivities')]
    private ?Room $room = null;

    #[ORM\ManyToOne(inversedBy: 'roomActivities')]
    private ?TeachingActivity $Activity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): static
    {
        $this->room = $room;

        return $this;
    }

    public function getActivity(): ?TeachingActivity
    {
        return $this->Activity;
    }

    public function setActivity(?TeachingActivity $Activity): static
    {
        $this->Activity = $Activity;

        return $this;
    }
}
