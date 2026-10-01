<?php

namespace App\Entity;

use App\Repository\ResourceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ResourceRepository::class)]
class Resource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['resource:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'resources')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['resource:read'])]
    private ?ResourceData $resource = null;

    #[ORM\Column]
    #[Groups(['resource:read'])]
    private ?int $duration = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $peremption = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 7)]
    #[Groups(['resource:read'])]
    private ?string $latitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 7)]
    #[Groups(['resource:read'])]
    private ?string $longitude = null;

    #[ORM\Column]
    #[Groups(['resource:read'])]
    private ?bool $isCollected = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $collectedAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['resource:read'])]
    private ?int $activationRadius = null;

    #[ORM\Column]
    #[Groups(['resource:read'])]
    private ?bool $isCollectable = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $city = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResource(): ?ResourceData
    {
        return $this->resource;
    }

    public function setResource(?ResourceData $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getPeremption(): ?\DateTimeInterface
    {
        return $this->peremption;
    }

    public function setPeremption(?\DateTimeInterface $peremption): static
    {
        $this->peremption = $peremption;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function isCollected(): ?bool
    {
        return $this->isCollected;
    }

    public function setIsCollected(bool $isCollected): static
    {
        $this->isCollected = $isCollected;

        return $this;
    }

    public function getCollectedAt(): ?\DateTimeImmutable
    {
        return $this->collectedAt;
    }

    public function setCollectedAt(?\DateTimeImmutable $collectedAt): static
    {
        $this->collectedAt = $collectedAt;

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

    public function getActivationRadius(): ?int
    {
        return $this->activationRadius;
    }

    public function setActivationRadius(int $activationRadius): static
    {
        $this->activationRadius = $activationRadius;

        return $this;
    }

    public function isCollectable(): ?bool
    {
        return $this->isCollectable;
    }

    public function setIsCollectable(bool $isCollectable): static
    {
        $this->isCollectable = $isCollectable;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }
}
