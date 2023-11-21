<?php

namespace App\Entity;

use App\Repository\UserInClassRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserInClassRepository::class)]
class UserInClass
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userInClasses')]
    private ?UserEntity $UserEntity = null;

    #[ORM\ManyToOne(inversedBy: 'userInClasses')]
    private ?ClassEntity $class = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $relationshipType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserEntity(): ?UserEntity
    {
        return $this->UserEntity;
    }

    public function setUserEntity(?UserEntity $UserEntity): static
    {
        $this->UserEntity = $UserEntity;

        return $this;
    }

    public function getClass(): ?ClassEntity
    {
        return $this->class;
    }

    public function setClass(?ClassEntity $class): static
    {
        $this->class = $class;

        return $this;
    }

    public function getRelationshipType(): ?string
    {
        return $this->relationshipType;
    }

    public function setRelationshipType(?string $relationshipType): static
    {
        $this->relationshipType = $relationshipType;

        return $this;
    }
}
