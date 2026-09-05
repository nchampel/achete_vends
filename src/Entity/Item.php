<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $payPrice = null;

    #[ORM\Column]
    private ?float $sellPrice = null;

    #[ORM\Column]
    private ?int $world = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'item', targetEntity: StockItem::class, orphanRemoval: true)]
    private Collection $stockItems;

    public function __construct()
    {
        $this->stockItems = new ArrayCollection();
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

    public function getPayPrice(): ?float
    {
        return $this->payPrice;
    }

    public function setPayPrice(float $payPrice): static
    {
        $this->payPrice = $payPrice;

        return $this;
    }

    public function getSellPrice(): ?float
    {
        return $this->sellPrice;
    }

    public function setSellPrice(float $sellPrice): static
    {
        $this->sellPrice = $sellPrice;

        return $this;
    }

    public function getWorld(): ?int
    {
        return $this->world;
    }

    public function setWorld(int $world): static
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
     * @return Collection<int, StockItem>
     */
    public function getStockItems(): Collection
    {
        return $this->stockItems;
    }

    public function addStockItem(StockItem $stockItem): static
    {
        if (!$this->stockItems->contains($stockItem)) {
            $this->stockItems->add($stockItem);
            $stockItem->setItem($this);
        }

        return $this;
    }

    public function removeStockItem(StockItem $stockItem): static
    {
        if ($this->stockItems->removeElement($stockItem)) {
            // set the owning side to null (unless already changed)
            if ($stockItem->getItem() === $this) {
                $stockItem->setItem(null);
            }
        }

        return $this;
    }
}
