<?php

namespace App\Entity;

use App\Repository\WorldRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorldRepository::class)]
class World
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $isUnlocked = null;

    #[ORM\ManyToOne(inversedBy: 'worlds')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'worlds')]
    #[ORM\JoinColumn(nullable: false)]
    private ?WorldData $world = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isUnlocked(): ?bool
    {
        return $this->isUnlocked;
    }

    public function setIsUnlocked(bool $isUnlocked): static
    {
        $this->isUnlocked = $isUnlocked;

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

    public function getWorld(): ?WorldData
    {
        return $this->world;
    }

    public function setWorld(?WorldData $world): static
    {
        $this->world = $world;

        return $this;
    }
}
