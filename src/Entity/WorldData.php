<?php

namespace App\Entity;

use App\Repository\WorldDataRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: WorldDataRepository::class)]
class WorldData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['user:read'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $number = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\OneToMany(mappedBy: 'world', targetEntity: World::class, orphanRemoval: true)]
    private Collection $worlds;

    public function __construct()
    {
        $this->worlds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

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

    /**
     * @return Collection<int, World>
     */
    public function getWorlds(): Collection
    {
        return $this->worlds;
    }

    // public function addWorld(World $world): static
    // {
    //     if (!$this->worlds->contains($world)) {
    //         $this->worlds->add($world);
    //         $world->setWorld($this);
    //     }

    //     return $this;
    // }

    // public function removeWorld(World $world): static
    // {
    //     if ($this->worlds->removeElement($world)) {
    //         // set the owning side to null (unless already changed)
    //         if ($world->getWorld() === $this) {
    //             $world->setWorld(null);
    //         }
    //     }

    //     return $this;
    // }

    public function __toString(): string
    {
        return $this->name ?? '';
    }
}
