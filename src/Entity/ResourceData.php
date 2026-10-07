<?php

namespace App\Entity;

use App\Repository\ResourceDataRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ResourceDataRepository::class)]
class ResourceData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['resource:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $imageUrl = null;

    #[ORM\Column(length: 50)]
    #[Groups(['resource:read'])]
    private ?string $type = null;

    #[ORM\Column]
    #[Groups(['resource:read'])]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'resourceData')]
    #[ORM\JoinColumn(nullable: false)]
    private ?WorldData $world = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'resource', targetEntity: Resource::class, orphanRemoval: true)]
    private Collection $resources;

    #[ORM\Column]
    private ?int $repopTime = null;

    #[ORM\Column]
    private ?int $collectableTime = null;

    public function __construct()
    {
        $this->resources = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;

        return $this;
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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Resource>
     */
    public function getResources(): Collection
    {
        return $this->resources;
    }

    public function addResource(Resource $resource): static
    {
        if (!$this->resources->contains($resource)) {
            $this->resources->add($resource);
            $resource->setResource($this);
        }

        return $this;
    }

    public function removeResource(Resource $resource): static
    {
        if ($this->resources->removeElement($resource)) {
            // set the owning side to null (unless already changed)
            if ($resource->getResource() === $this) {
                $resource->setResource(null);
            }
        }

        return $this;
    }

    public function getRepopTime(): ?int
    {
        return $this->repopTime;
    }

    public function setRepopTime(int $repopTime): static
    {
        $this->repopTime = $repopTime;

        return $this;
    }

    public function getCollectableTime(): ?int
    {
        return $this->collectableTime;
    }

    public function setCollectableTime(int $collectableTime): static
    {
        $this->collectableTime = $collectableTime;

        return $this;
    }
}
