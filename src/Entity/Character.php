<?php

namespace App\Entity;

use App\Repository\CharacterRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

#[ORM\Entity(repositoryClass: CharacterRepository::class)]
#[ORM\Table(name: '`character`')]
class Character
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $Strength = null;

    #[ORM\Column]
    private ?float $Speed = null;

    #[ORM\Column]
    private ?float $Durability = null;

    #[ORM\Column]
    private ?float $Power = null;

    #[ORM\Column]
    private ?float $Combat = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStrength(): ?float
    {
        return $this->Strength;
    }

    public function setStrength(float $Strength): static
    {
        $this->Strength = $Strength;

        return $this;
    }

    public function getSpeed(): ?float
    {
        return $this->Speed;
    }

    public function setSpeed(float $Speed): static
    {
        $this->Speed = $Speed;

        return $this;
    }

    public function getDurability(): ?float
    {
        return $this->Durability;
    }

    public function setDurability(float $Durability): static
    {
        $this->Durability = $Durability;

        return $this;
    }

    public function getPower(): ?float
    {
        return $this->Power;
    }

    public function setPower(float $Power): static
    {
        $this->Power = $Power;

        return $this;
    }

    public function getCombat(): ?float
    {
        return $this->Combat;
    }

    public function setCombat(float $Combat): static
    {
        $this->Combat = $Combat;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }
}
