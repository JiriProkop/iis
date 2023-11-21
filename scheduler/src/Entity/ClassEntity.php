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

    #[ORM\Column(length: 5)]
    private ?string $abbr = "";

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $anotation = null;

    #[ORM\Column]
    private ?int $credit_number = null;

    #[ORM\OneToMany(mappedBy: 'Class', targetEntity: TeachingActivity::class, orphanRemoval: true)]
    private Collection $teachingActivities;

    #[ORM\OneToMany(mappedBy: 'class', targetEntity: UserInClass::class)]
    private Collection $userInClasses;

    public function __construct()
    {
        $this->teachingActivities = new ArrayCollection();
        $this->userInClasses = new ArrayCollection();
    }

    //TODO tady mozna bude potreba udelat jeste setter, nevim jak to funguje. V user kdyztak uz je, takze se possibly inspirovat tam

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAbbr(): ?string
    {
        return $this->abbr;
    }

    public function setAbbr(string $abbr): static
    {
        $this->abbr = $abbr;
        return $this;
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

    public function getAnotation(): ?string
    {
        return $this->anotation;
    }

    public function setAnotation(string $anotation): static
    {
        $this->anotation = $anotation;

        return $this;
    }

    public function getCreditNumber(): ?int
    {
        return $this->credit_number;
    }

    public function setCreditNumber(int $credit_number): static
    {
        $this->credit_number = $credit_number;

        return $this;
    }

    /**
     * @return Collection<int, TeachingActivity>
     */
    public function getTeachingActivities(): Collection
    {
        return $this->teachingActivities;
    }

    public function addTeachingActivity(TeachingActivity $teachingActivity): static
    {
        if (!$this->teachingActivities->contains($teachingActivity)) {
            $this->teachingActivities->add($teachingActivity);
            $teachingActivity->setClass($this);
        }

        return $this;
    }

    public function removeTeachingActivity(TeachingActivity $teachingActivity): static
    {
        if ($this->teachingActivities->removeElement($teachingActivity)) {
            // set the owning side to null (unless already changed)
            if ($teachingActivity->getClass() === $this) {
                $teachingActivity->setClass(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserInClass>
     */
    public function getUserInClasses(): Collection
    {
        return $this->userInClasses;
    }

    public function addUserInClass(UserInClass $userInClass): static
    {
        if (!$this->userInClasses->contains($userInClass)) {
            $this->userInClasses->add($userInClass);
            $userInClass->setClass($this);
        }

        return $this;
    }

    public function removeUserInClass(UserInClass $userInClass): static
    {
        if ($this->userInClasses->removeElement($userInClass)) {
            // set the owning side to null (unless already changed)
            if ($userInClass->getClass() === $this) {
                $userInClass->setClass(null);
            }
        }

        return $this;
    }
}
