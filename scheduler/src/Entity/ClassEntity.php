<?php

namespace App\Entity;

use App\Repository\ClassEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClassEntityRepository::class)]
class ClassEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $Abbreviation = null;

    #[ORM\Column(length: 100)]
    private ?string $Name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $Anotation = null;

    #[ORM\Column]
    private ?int $Credits = null;

    #[ORM\ManyToOne(inversedBy: 'Guatanted_classes')]
    private ?PersonEntity $Guarantor = null;

    #[ORM\ManyToMany(targetEntity: PersonEntity::class, inversedBy: 'Classes')]
    private Collection $People;

    #[ORM\OneToMany(mappedBy: 'Class', targetEntity: ClassActivityEntity::class, orphanRemoval: true)]
    private Collection $Activities;

    public function __construct()
    {
        $this->People = new ArrayCollection();
        $this->Activities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAbbreviation(): ?string
    {
        return $this->Abbreviation;
    }

    public function setAbbreviation(string $Abbreviation): static
    {
        $this->Abbreviation = $Abbreviation;

        return $this;
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

    public function getAnotation(): ?string
    {
        return $this->Anotation;
    }

    public function setAnotation(string $Anotation): static
    {
        $this->Anotation = $Anotation;

        return $this;
    }

    public function getCredits(): ?int
    {
        return $this->Credits;
    }

    public function setCredits(int $Credits): static
    {
        $this->Credits = $Credits;

        return $this;
    }

    public function getGuarantor(): ?PersonEntity
    {
        return $this->Guarantor;
    }

    public function setGuarantor(?PersonEntity $Guarantor): static
    {
        $this->Guarantor = $Guarantor;

        return $this;
    }

    /**
     * @return Collection<int, PersonEntity>
     */
    public function getPeople(): Collection
    {
        return $this->People;
    }

    public function addPerson(PersonEntity $person): static
    {
        if (!$this->People->contains($person)) {
            $this->People->add($person);
        }

        return $this;
    }

    public function removePeople(PersonEntity $person): static
    {
        $this->People->removeElement($person);

        return $this;
    }

    /**
     * @return Collection<int, ClassActivityEntity>
     */
    public function getActivities(): Collection
    {
        return $this->Activities;
    }

    public function addActivity(ClassActivityEntity $activity): static
    {
        if (!$this->Activities->contains($activity)) {
            $this->Activities->add($activity);
            $activity->setClass($this);
        }

        return $this;
    }

    public function removeActivity(ClassActivityEntity $activity): static
    {
        if ($this->Activities->removeElement($activity)) {
            // set the owning side to null (unless already changed)
            if ($activity->getClass() === $this) {
                $activity->setClass(null);
            }
        }

        return $this;
    }
}
