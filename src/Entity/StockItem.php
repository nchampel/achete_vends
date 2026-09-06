<?php

namespace App\Entity;

use App\Repository\StockItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockItemRepository::class)]
class StockItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'stockItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Item $item = null;

    #[ORM\Column]
    private ?float $finalPayPrice = null;

    #[ORM\Column]
    private ?float $finalSellPrice = null;

    #[ORM\Column]
    private ?bool $isBought = false;

    #[ORM\Column]
    private ?bool $isSold = false;

    #[ORM\Column]
    private ?bool $isBuyable = false;

    #[ORM\ManyToOne(inversedBy: 'stockItems')]
    private ?User $user = null;

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
}
