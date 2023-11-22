<?php

namespace App\Entity;

use App\Repository\PersonalActivityEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonalActivityEntityRepository::class)]
class PersonalActivityEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Description = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $Repetition = null;

    #[ORM\ManyToOne(inversedBy: 'PersonalActivities')]
    private ?RoomEntity $Room = null;

    #[ORM\OneToMany(mappedBy: 'PersonalActivity', targetEntity: ScheduleWindowEntity::class)]
    private Collection $ScheduledWindows;

    #[ORM\ManyToOne(inversedBy: 'PersonalActivities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PersonEntity $Person = null;

    public function __construct()
    {
        $this->ScheduledWindows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): static
    {
        $this->Description = $Description;

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

    public function getRoom(): ?RoomEntity
    {
        return $this->Room;
    }

    public function setRoom(?RoomEntity $Room): static
    {
        $this->Room = $Room;

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
            $scheduledWindow->setPersonalActivity($this);
        }

        return $this;
    }

    public function removeScheduledWindow(ScheduleWindowEntity $scheduledWindow): static
    {
        if ($this->ScheduledWindows->removeElement($scheduledWindow)) {
            // set the owning side to null (unless already changed)
            if ($scheduledWindow->getPersonalActivity() === $this) {
                $scheduledWindow->setPersonalActivity(null);
            }
        }

        return $this;
    }

    public function getPerson(): ?PersonEntity
    {
        return $this->Person;
    }

    public function setPerson(?PersonEntity $Person): static
    {
        $this->Person = $Person;

        return $this;
    }
}
