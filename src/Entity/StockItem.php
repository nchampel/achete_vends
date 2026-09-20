<?php

namespace App\Entity;

use App\Repository\StockItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: StockItemRepository::class)]
class StockItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['stockItem:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'stockItems')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['stockItem:read'])]
    private ?Item $item = null;

    #[ORM\Column]
    #[Groups(['stockItem:read'])]
    private ?float $finalPayPrice = null;

    #[ORM\Column]
    private ?float $finalSellPrice = null;

    #[ORM\Column]
    #[Groups(['stockItem:read'])]
    private ?bool $isBought = false;

    #[ORM\Column]
    #[Groups(['stockItem:read'])]
    private ?bool $isSold = false;

    #[ORM\Column]
    private ?bool $isBuyable = false;

    #[ORM\ManyToOne(inversedBy: 'stockItems')]
    private ?User $user = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['stockItem:read'])]
    private ?float $userSellPrice = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $boughtAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $soldAt = null;

    #[ORM\Column]
    #[Groups(['stockItem:read'])]
    private ?bool $isAnalysed = false;

    #[ORM\Column(nullable: true)]
    #[Groups(['stockItem:read'])]
    private ?\DateTimeImmutable $analysedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['stockItem:read'])]
    private ?int $number = null;

    #[ORM\Column]
    #[Groups(['stockItem:read'])]
    private ?bool $isPendingSale = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $pendingSaleAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item): static
    {
        $this->item = $item;

        return $this;
    }

    public function getFinalPayPrice(): ?float
    {
        return $this->finalPayPrice;
    }

    public function setFinalPayPrice(float $finalPayPrice): static
    {
        $this->finalPayPrice = $finalPayPrice;

        return $this;
    }

    public function getFinalSellPrice(): ?float
    {
        return $this->finalSellPrice;
    }

    public function setFinalSellPrice(float $finalSellPrice): static
    {
        $this->finalSellPrice = $finalSellPrice;

        return $this;
    }

    public function isBought(): ?bool
    {
        return $this->isBought;
    }

    public function setIsBought(bool $isBought): static
    {
        $this->isBought = $isBought;

        return $this;
    }

    public function isSold(): ?bool
    {
        return $this->isSold;
    }

    public function setIsSold(bool $isSold): static
    {
        $this->isSold = $isSold;

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

    public function isBuyable(): ?bool
    {
        return $this->isBuyable;
    }

    public function setIsBuyable(bool $isBuyable): static
    {
        $this->isBuyable = $isBuyable;

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

    public function getUserSellPrice(): ?float
    {
        return $this->userSellPrice;
    }

    public function setUserSellPrice(float $userSellPrice): static
    {
        $this->userSellPrice = $userSellPrice;

        return $this;
    }

    public function getBoughtAt(): ?\DateTimeImmutable
    {
        return $this->boughtAt;
    }

    public function setBoughtAt(\DateTimeImmutable $boughtAt): static
    {
        $this->boughtAt = $boughtAt;

        return $this;
    }

    public function getSoldAt(): ?\DateTimeImmutable
    {
        return $this->soldAt;
    }

    public function setSoldAt(?\DateTimeImmutable $soldAt): static
    {
        $this->soldAt = $soldAt;

        return $this;
    }

    public function isAnalysed(): ?bool
    {
        return $this->isAnalysed;
    }

    public function setIsAnalysed(bool $isAnalysed): static
    {
        $this->isAnalysed = $isAnalysed;

        return $this;
    }

    public function getAnalysedAt(): ?\DateTimeImmutable
    {
        return $this->analysedAt;
    }

    public function setAnalysedAt(\DateTimeImmutable $analysedAt): static
    {
        $this->analysedAt = $analysedAt;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(?int $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function isPendingSale(): ?bool
    {
        return $this->isPendingSale;
    }

    public function setIsPendingSale(bool $isPendingSale): static
    {
        $this->isPendingSale = $isPendingSale;

        return $this;
    }

    public function getPendingSaleAt(): ?\DateTimeImmutable
    {
        return $this->pendingSaleAt;
    }

    public function setPendingSaleAt(\DateTimeImmutable $pendingSaleAt): static
    {
        $this->pendingSaleAt = $pendingSaleAt;

        return $this;
    }
}
