<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['pseudo'], message: 'There is already an account with this pseudo')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $pseudo = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private ?float $money = 0;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: StockItem::class)]
    private Collection $stockItems;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: World::class, orphanRemoval: true)]
    private Collection $worlds;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?World $currentWorld = null;

    public function __construct()
    {
        $this->stockItems = new ArrayCollection();
        $this->worlds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->pseudo;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->pseudo;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getMoney(): ?float
    {
        return $this->money;
    }

    public function setMoney(float $money): static
    {
        $this->money = $money;

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
            $stockItem->setUser($this);
        }

        return $this;
    }

    public function removeStockItem(StockItem $stockItem): static
    {
        if ($this->stockItems->removeElement($stockItem)) {
            // set the owning side to null (unless already changed)
            if ($stockItem->getUser() === $this) {
                $stockItem->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, World>
     */
    public function getWorlds(): Collection
    {
        return $this->worlds;
    }

    public function addWorld(World $world): static
    {
        if (!$this->worlds->contains($world)) {
            $this->worlds->add($world);
            $world->setUser($this);
        }

        return $this;
    }

    public function removeWorld(World $world): static
    {
        if ($this->worlds->removeElement($world)) {
            if ($world->getUser() === $this) {
                $world->setUser(null);
            }
        }

        if ($this->currentWorld === $world) {
            $this->currentWorld = null;
        }

        return $this;
    }

    public function getCurrentWorld(): ?World
    {
        return $this->currentWorld;
    }

    public function setCurrentWorld(?World $currentWorld): static
    {
        $this->currentWorld = $currentWorld;

        return $this;
    }

    public function __toString(): string
    {
        return $this->pseudo ?? '';
    }

}
