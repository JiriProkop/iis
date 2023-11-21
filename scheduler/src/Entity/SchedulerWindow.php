<?php

namespace App\Entity;

use App\Repository\SchedulerWindowRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SchedulerWindowRepository::class)]
class SchedulerWindow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $start = null;

    #[ORM\ManyToOne(inversedBy: 'schedule_windows')]
    private ?OwnActivity $personalActivity = null;

    #[ORM\ManyToOne(inversedBy: 'scheduleWindows')]
    private ?TeachingActivity $teachingActivity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): static
    {
        $this->start = $start;

        return $this;
    }

    public function getPersonalActivity(): ?OwnActivity
    {
        return $this->personalActivity;
    }

    public function setPersonalActivity(?OwnActivity $personalActivity): static
    {
        $this->personalActivity = $personalActivity;

        return $this;
    }

    public function getTeachingActivity(): ?TeachingActivity
    {
        return $this->teachingActivity;
    }

    public function setTeachingActivity(?TeachingActivity $teachingActivity): static
    {
        $this->teachingActivity = $teachingActivity;

        return $this;
    }
}
