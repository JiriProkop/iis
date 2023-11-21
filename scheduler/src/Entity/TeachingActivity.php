<?php

namespace App\Entity;

use App\Repository\TeachingActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeachingActivityRepository::class)]
class TeachingActivity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $repetition = null;

    #[ORM\Column]
    private ?int $length = null;

    #[ORM\OneToMany(mappedBy: 'Activity', targetEntity: RoomActivity::class)]
    private Collection $roomActivities;

    #[ORM\OneToMany(mappedBy: 'teachingActivity', targetEntity: SchedulerWindow::class)]
    private Collection $scheduleWindows;

    #[ORM\ManyToOne(inversedBy: 'teachingActivities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserEntity $Teacher = null;

    #[ORM\ManyToOne(inversedBy: 'teachingActivities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ClassEntity $Class = null;

    public function __construct()
    {
        $this->roomActivities = new ArrayCollection();
        $this->scheduleWindows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(int $length): static
    {
        $this->length = $length;

        return $this;
    }

    /**
     * @return Collection<int, RoomActivity>
     */
    public function getRoomActivities(): Collection
    {
        return $this->roomActivities;
    }

    public function addRoomActivity(RoomActivity $roomActivity): static
    {
        if (!$this->roomActivities->contains($roomActivity)) {
            $this->roomActivities->add($roomActivity);
            $roomActivity->setActivity($this);
        }

        return $this;
    }

    public function removeRoomActivity(RoomActivity $roomActivity): static
    {
        if ($this->roomActivities->removeElement($roomActivity)) {
            // set the owning side to null (unless already changed)
            if ($roomActivity->getActivity() === $this) {
                $roomActivity->setActivity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SchedulerWindow>
     */
    public function getScheduleWindows(): Collection
    {
        return $this->scheduleWindows;
    }

    public function addScheduleWindow(SchedulerWindow $scheduleWindow): static
    {
        if (!$this->scheduleWindows->contains($scheduleWindow)) {
            $this->scheduleWindows->add($scheduleWindow);
            $scheduleWindow->setTeachingActivity($this);
        }

        return $this;
    }

    public function removeScheduleWindow(SchedulerWindow $scheduleWindow): static
    {
        if ($this->scheduleWindows->removeElement($scheduleWindow)) {
            // set the owning side to null (unless already changed)
            if ($scheduleWindow->getTeachingActivity() === $this) {
                $scheduleWindow->setTeachingActivity(null);
            }
        }

        return $this;
    }

    public function getTeacher(): ?UserEntity
    {
        return $this->Teacher;
    }

    public function setTeacher(?UserEntity $Teacher): static
    {
        $this->Teacher = $Teacher;

        return $this;
    }

    public function getClass(): ?ClassEntity
    {
        return $this->Class;
    }

    public function setClass(?ClassEntity $Class): static
    {
        $this->Class = $Class;

        return $this;
    }
}
