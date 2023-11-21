<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\OneToMany(mappedBy: 'room', targetEntity: RoomActivity::class)]
    private Collection $roomActivities;

    #[ORM\OneToMany(mappedBy: 'room', targetEntity: OwnActivity::class)]
    private Collection $personal_activities;

    public function __construct()
    {
        $this->roomActivities = new ArrayCollection();
        $this->personal_activities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

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
            $roomActivity->setRoom($this);
        }

        return $this;
    }

    public function removeRoomActivity(RoomActivity $roomActivity): static
    {
        if ($this->roomActivities->removeElement($roomActivity)) {
            // set the owning side to null (unless already changed)
            if ($roomActivity->getRoom() === $this) {
                $roomActivity->setRoom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, OwnActivity>
     */
    public function getPersonalActivities(): Collection
    {
        return $this->personal_activities;
    }

    public function addPersonalActivity(OwnActivity $personalActivity): static
    {
        if (!$this->personal_activities->contains($personalActivity)) {
            $this->personal_activities->add($personalActivity);
            $personalActivity->setRoom($this);
        }

        return $this;
    }

    public function removePersonalActivity(OwnActivity $personalActivity): static
    {
        if ($this->personal_activities->removeElement($personalActivity)) {
            // set the owning side to null (unless already changed)
            if ($personalActivity->getRoom() === $this) {
                $personalActivity->setRoom(null);
            }
        }

        return $this;
    }
}
