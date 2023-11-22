<?php

namespace App\Entity;

use App\Repository\PersonEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonEntityRepository::class)]
class PersonEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Email = null;

    #[ORM\Column(length: 255)]
    private ?string $Password = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $Role = null;

    #[ORM\OneToMany(mappedBy: 'Guarantor', targetEntity: ClassEntity::class)]
    private Collection $Guatanted_classes;

    #[ORM\ManyToMany(targetEntity: ClassEntity::class, mappedBy: 'People')]
    private Collection $Classes;

    #[ORM\OneToMany(mappedBy: 'Person', targetEntity: PersonalActivityEntity::class, orphanRemoval: true)]
    private Collection $PersonalActivities;

    #[ORM\OneToMany(mappedBy: 'Teacher', targetEntity: ClassActivityEntity::class)]
    private Collection $ClassActivities;

    #[ORM\Column(length: 10)]
    private ?string $Login = null;

    public function __construct()
    {
        $this->Guatanted_classes = new ArrayCollection();
        $this->Classes = new ArrayCollection();
        $this->Teaching_in_classes = new ArrayCollection();
        $this->PersonalActivities = new ArrayCollection();
        $this->ClassActivities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): static
    {
        $this->Email = $Email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->Password;
    }

    public function setPassword(string $Password): static
    {
        $this->Password = $Password;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->Role;
    }

    public function setRole(?string $Role): static
    {
        $this->Role = $Role;

        return $this;
    }

    /**
     * @return Collection<int, ClassEntity>
     */
    public function getGuatantedClasses(): Collection
    {
        return $this->Guatanted_classes;
    }

    public function addGuatantedClass(ClassEntity $guatantedClass): static
    {
        if (!$this->Guatanted_classes->contains($guatantedClass)) {
            $this->Guatanted_classes->add($guatantedClass);
            $guatantedClass->setGuarantor($this);
        }

        return $this;
    }

    public function removeGuatantedClass(ClassEntity $guatantedClass): static
    {
        if ($this->Guatanted_classes->removeElement($guatantedClass)) {
            // set the owning side to null (unless already changed)
            if ($guatantedClass->getGuarantor() === $this) {
                $guatantedClass->setGuarantor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ClassEntity>
     */
    public function getClasses(): Collection
    {
        return $this->Classes;
    }

    public function addClass(ClassEntity $class): static
    {
        if (!$this->Classes->contains($class)) {
            $this->Classes->add($class);
            $class->addStudent($this);
        }

        return $this;
    }

    public function removeClass(ClassEntity $class): static
    {
        if ($this->Classes->removeElement($class)) {
            $class->removeStudent($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, ClassEntity>
     */
    public function getTeachingInClasses(): Collection
    {
        return $this->Teaching_in_classes;
    }

    public function addTeachingInClass(ClassEntity $teachingInClass): static
    {
        if (!$this->Teaching_in_classes->contains($teachingInClass)) {
            $this->Teaching_in_classes->add($teachingInClass);
            $teachingInClass->addTeacher($this);
        }

        return $this;
    }

    public function removeTeachingInClass(ClassEntity $teachingInClass): static
    {
        if ($this->Teaching_in_classes->removeElement($teachingInClass)) {
            $teachingInClass->removeTeacher($this);
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
            $personalActivity->setPerson($this);
        }

        return $this;
    }

    public function removePersonalActivity(PersonalActivityEntity $personalActivity): static
    {
        if ($this->PersonalActivities->removeElement($personalActivity)) {
            // set the owning side to null (unless already changed)
            if ($personalActivity->getPerson() === $this) {
                $personalActivity->setPerson(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ClassActivityEntity>
     */
    public function getClassActivities(): Collection
    {
        return $this->ClassActivities;
    }

    public function addClassActivity(ClassActivityEntity $classActivity): static
    {
        if (!$this->ClassActivities->contains($classActivity)) {
            $this->ClassActivities->add($classActivity);
            $classActivity->setTeacher($this);
        }

        return $this;
    }

    public function removeClassActivity(ClassActivityEntity $classActivity): static
    {
        if ($this->ClassActivities->removeElement($classActivity)) {
            // set the owning side to null (unless already changed)
            if ($classActivity->getTeacher() === $this) {
                $classActivity->setTeacher(null);
            }
        }

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->Login;
    }

    public function setLogin(string $Login): static
    {
        $this->Login = $Login;

        return $this;
    }
}
