<?php

namespace App\Entity;

use App\Repository\WorldRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: WorldRepository::class)]
class World
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $isUnlocked = false;

    #[ORM\ManyToOne(inversedBy: 'worlds')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

//     #[ORM\ManyToOne(inversedBy: 'worlds')]
// #[ORM\JoinColumn(nullable: false)]
// private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'worlds')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user:read'])]
    private ?WorldData $worldData = null;

    // #[ORM\ManyToOne(inversedBy: 'world')]
    // #[ORM\JoinColumn(nullable: false)]
    // private ?User $user2 = null;

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

    public function getWorldData(): ?WorldData
    {
        return $this->worldData;
    }

    public function setWorldData(?WorldData $worldData): static
    {
        $this->worldData = $worldData;

        return $this;
    }

    // public function unlockWorld(WorldData $world, WorldRepository $repoWorld,  WorldDataRepository $repoData, EntityManagerInterface $entityManager){
    //     /** @var \App\Entity\User $user */
    //     $user = $this->getUser();
        
    //     $currentWorld = $user->getCurrentWorld();

    //     if (!$currentWorld) {
    //         return;
    //     }

    //     $newWorldNumber = $currentWorld->getWorldData()->getNumber() + 1;
    //     // $newWorldNumber = $user->getWorlds()[0]->getWorld()->getNumber() + 1;
    //     $newWorldData = $repoData->findOneBy(['number' => $newWorldNumber]);
    //     if($newWorldData->getAmount() <= $user->getMoney()){
    //         $newWorld = $repoWorld->findOneBy(['user' => $user]);
    //         $newWorld->setIsUnlocked(true);
    //         $entityManager->persist($newWorld);

    //         $user->setMoney($user->getMoney() - $newWorldData->getAmount());
    //         $user->addWorld($newWorld);
    //         $entityManager->persist($user);

    //         $entityManager->flush();
    //     }
    // }

    // public function getUser2(): ?User
    // {
    //     return $this->user2;
    // }

    // public function setUser2(?User $user2): static
    // {
    //     $this->user2 = $user2;

    //     return $this;
    // }

    public function getName(){
        return $this->getWorldData()->getName();
    }

    public function __toString(): string
{
    return $this->getName() ?? '';
}
}
