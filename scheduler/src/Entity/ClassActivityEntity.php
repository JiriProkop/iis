<?php

namespace App\Entity;

use App\Repository\ClassActivityEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClassActivityEntityRepository::class)]
class ClassActivityEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $Repetition = null;

    #[ORM\Column]
    private ?int $Length = null;

    #[ORM\OneToMany(mappedBy: 'ClassActivity', targetEntity: ScheduleWindowEntity::class)]
    private Collection $ScheduledWindows;

    #[ORM\ManyToMany(targetEntity: RoomEntity::class, inversedBy: 'TeachedActivities')]
    private Collection $Rooms;

    #[ORM\ManyToOne(inversedBy: 'Activities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ClassEntity $Class = null;

    #[ORM\ManyToOne(inversedBy: 'ClassActivities')]
    private ?PersonEntity $Teacher = null;

    #[ORM\Column(nullable: true)]
    private ?bool $draft = null;

    public function __construct()
    {
        $this->ScheduledWindows = new ArrayCollection();
        $this->Rooms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): static
    {
        $this->Name = $Name;

        return $this;
    }

    public function getRepetition(): ?string
    {
        return $this->Repetition;
    }

    public function setRepetition(?string $Repetition): static
    {
        $this->Repetition = $Repetition;

        return $this;
    }

    public function getLength(): ?int
    {
        return $this->Length;
    }

    public function setLength(int $Length): static
    {
        $this->Length = $Length;

        return $this;
    }

    /**
     * @return Collection<int, ScheduleWindowEntity>
     */
    public function getScheduledWindows(): Collection
    {
        return $this->ScheduledWindows;
    }

    public function addScheduledWindow(ScheduleWindowEntity $scheduledWindow): static
    {
        if (!$this->ScheduledWindows->contains($scheduledWindow)) {
            $this->ScheduledWindows->add($scheduledWindow);
            $scheduledWindow->setClassActivity($this);
        }

        return $this;
    }

    public function removeScheduledWindow(ScheduleWindowEntity $scheduledWindow): static
    {
        if ($this->ScheduledWindows->removeElement($scheduledWindow)) {
            // set the owning side to null (unless already changed)
            if ($scheduledWindow->getClassActivity() === $this) {
                $scheduledWindow->setClassActivity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RoomEntity>
     */
    public function getRooms(): Collection
    {
        return $this->Rooms;
    }

    public function addRoom(RoomEntity $room): static
    {
        if (!$this->Rooms->contains($room)) {
            $this->Rooms->add($room);
        }

        return $this;
    }

    public function removeRoom(RoomEntity $room): static
    {
        $this->Rooms->removeElement($room);

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

    public function getTeacher(): ?PersonEntity
    {
        return $this->Teacher;
    }

    public function setTeacher(?PersonEntity $Teacher): static
    {
        $this->Teacher = $Teacher;

        return $this;
    }

    public function isDraft(): ?bool
    {
        return $this->draft;
    }

    public function setDraft(?bool $draft): static
    {
        $this->draft = $draft;

        return $this;
    }
}
