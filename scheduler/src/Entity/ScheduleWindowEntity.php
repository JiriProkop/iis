<?php

namespace App\Entity;

use App\Repository\ScheduleWindowEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScheduleWindowEntityRepository::class)]
class ScheduleWindowEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $Start = null;

    #[ORM\ManyToOne(inversedBy: 'ScheduledWindows')]
    private ?ClassActivityEntity $ClassActivity = null;

    #[ORM\ManyToOne(inversedBy: 'ScheduledWindows')]
    private ?PersonalActivityEntity $PersonalActivity = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $End = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->Start;
    }

    public function setStart(\DateTimeInterface $Start): static
    {
        $this->Start = $Start;

        return $this;
    }

    public function getClassActivity(): ?ClassActivityEntity
    {
        return $this->ClassActivity;
    }

    public function setClassActivity(?ClassActivityEntity $ClassActivity): static
    {
        $this->ClassActivity = $ClassActivity;

        return $this;
    }

    public function getPersonalActivity(): ?PersonalActivityEntity
    {
        return $this->PersonalActivity;
    }

    public function setPersonalActivity(?PersonalActivityEntity $PersonalActivity): static
    {
        $this->PersonalActivity = $PersonalActivity;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->End;
    }

    public function setEnd(?\DateTimeInterface $End): static
    {
        $this->End = $End;

        return $this;
    }
}
