<?php

namespace App\Entity;

use App\Repository\OwnActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OwnActivityRepository::class)]
class OwnActivity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $repetition = null;

    #[ORM\ManyToOne(inversedBy: 'personal_activities')]
    private ?Room $room = null;

    #[ORM\OneToMany(mappedBy: 'personalActivity', targetEntity: SchedulerWindow::class)]
    private Collection $schedule_windows;

    #[ORM\ManyToOne(inversedBy: 'ownActivities')]
    private ?UserEntity $teacher = null;

    public function __construct()
    {
        $this->schedule_windows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getRepetition(): ?string
    {
        return $this->repetition;
    }

    public function setRepetition(string $repetition): static
    {
        $this->repetition = $repetition;

        return $this;
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

    /**
     * @return Collection<int, SchedulerWindow>
     */
    public function getScheduleWindows(): Collection
    {
        return $this->schedule_windows;
    }

    public function addScheduleWindow(SchedulerWindow $scheduleWindow): static
    {
        if (!$this->schedule_windows->contains($scheduleWindow)) {
            $this->schedule_windows->add($scheduleWindow);
            $scheduleWindow->setPersonalActivity($this);
        }

        return $this;
    }

    public function removeScheduleWindow(SchedulerWindow $scheduleWindow): static
    {
        if ($this->schedule_windows->removeElement($scheduleWindow)) {
            // set the owning side to null (unless already changed)
            if ($scheduleWindow->getPersonalActivity() === $this) {
                $scheduleWindow->setPersonalActivity(null);
            }
        }

        return $this;
    }

    public function getTeacher(): ?UserEntity
    {
        return $this->teacher;
    }

    public function setTeacher(?UserEntity $teacher): static
    {
        $this->teacher = $teacher;

        return $this;
    }
}
