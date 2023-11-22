<?php

namespace App\Entity;

use App\Repository\RoomEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomEntityRepository::class)]
class RoomEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $Name = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $Type = null;

    #[ORM\ManyToMany(targetEntity: ClassActivityEntity::class, mappedBy: 'Rooms')]
    private Collection $TeachedActivities;

    #[ORM\OneToMany(mappedBy: 'Room', targetEntity: PersonalActivityEntity::class)]
    private Collection $PersonalActivities;

    public function __construct()
    {
        $this->TeachedActivities = new ArrayCollection();
        $this->PersonalActivities = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->Type;
    }

    public function setType(?string $Type): static
    {
        $this->Type = $Type;

        return $this;
    }

    /**
     * @return Collection<int, ClassActivityEntity>
     */
    public function getTeachedActivities(): Collection
    {
        return $this->TeachedActivities;
    }

    public function addTeachedActivity(ClassActivityEntity $teachedActivity): static
    {
        if (!$this->TeachedActivities->contains($teachedActivity)) {
            $this->TeachedActivities->add($teachedActivity);
            $teachedActivity->addRoom($this);
        }

        return $this;
    }

    public function removeTeachedActivity(ClassActivityEntity $teachedActivity): static
    {
        if ($this->TeachedActivities->removeElement($teachedActivity)) {
            $teachedActivity->removeRoom($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, PersonalActivityEntity>
     */
    public function getPersonalActivities(): Collection
    {
        return $this->PersonalActivities;
    }

    public function addPersonalActivity(PersonalActivityEntity $personalActivity): static
    {
        if (!$this->PersonalActivities->contains($personalActivity)) {
            $this->PersonalActivities->add($personalActivity);
            $personalActivity->setRoom($this);
        }

        return $this;
    }

    public function removePersonalActivity(PersonalActivityEntity $personalActivity): static
    {
        if ($this->PersonalActivities->removeElement($personalActivity)) {
            // set the owning side to null (unless already changed)
            if ($personalActivity->getRoom() === $this) {
                $personalActivity->setRoom(null);
            }
        }

        return $this;
    }
}
