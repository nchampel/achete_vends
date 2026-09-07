<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Item;
use App\Entity\StockItem;
use App\Repository\ItemRepository;
use App\Repository\StockItemRepository;
use Doctrine\ORM\EntityManagerInterface;

class ItemService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private StockItemRepository $stockItemRepository,
        // private WorldRepository $worldRepository,
    ) {
    }

    /**
     * Achat par le joueur d'un article.
     *
     * @return void
     *         achat et fixation du prix de vente.
     */
    public function buyItem(User $user, StockItem $stockItem, float $sellPrice): void
    {

        // $newWorldData = $this->stockItemRepository->findOneBy(['id' => $newWorldNumber]);
        /**  @var \App\Entity\StockItem $stockItem */
        if($stockItem->getFinalPayPrice() <= $user->getMoney()){
            // $stockItem = $this->worldRepository->findOneBy(['user' => $user, 'worldData' => $newWorldData]);
            $stockItem->setIsBought(true);
            $stockItem->setUser($user);
            $stockItem->setUserSellPrice($sellPrice);
            $stockItem->setBoughtAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
            $this->entityManager->persist($stockItem);

            $user->setMoney($user->getMoney() - $stockItem->getFinalPayPrice());
            $this->entityManager->persist($user);

            $this->entityManager->flush();
        } else {
            // pas assez d'argent
        }
    }
    /**
     *Mise en vente par le joueur d'un article.
     *
     * @return void
     *         mise en vente au prix de vente fixé par le joueur.
     */
    public function sellItem(User $user, StockItem $stockItem): void
    {

        // $newWorldData = $this->stockItemRepository->findOneBy(['id' => $newWorldNumber]);
        if(!$stockItem->isBought() || !$stockItem->getUser() || $stockItem->isSold()){
            return;
        }
        /**  @var \App\Entity\StockItem $stockItem */
        $userSellPrice = $stockItem->getUserSellPrice();
        $sellPrice = $stockItem->getItem()->getSellPrice();
        $percent = 0;

        $userSellPrice = 100;
        $sellPrice = 100;

        // on calcule le % positif ou négatif par rapport au prix conseillé
        $differencePercent = ($userSellPrice - $sellPrice) * 100 / $sellPrice;
        if($differencePercent <= -15){
            $percent = 95;
        }
        if(-15 < $differencePercent && $differencePercent <= -10){
            $percent = 90;
        }
        if(-10 < $differencePercent && $differencePercent <= -5){
            $percent = 85;
        }
        if(-5 < $differencePercent && $differencePercent < 0){
            $percent = 82;
        }
                        
        if($userSellPrice == $sellPrice){
            $percent = 88;
        }
        if(0 < $differencePercent && $differencePercent <= 5){
            $percent = 75;
        }
        if(5 < $differencePercent && $differencePercent <= 10){
            $percent = 55;
        }
        if(10 < $differencePercent && $differencePercent <= 15){
            $percent = 40;
        }
        if(15 < $differencePercent){
            $percent = 25;
        }
        // dump($percent);

        $sellNumber = random_int(1, 100);

        dump($sellNumber);

        if($percent > $sellNumber){
            $stockItem->setIsSold(true);
            $user->setMoney($user->getMoney() + $stockItem->getUserSellPrice());
            $this->entityManager->persist($stockItem);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } else {
            // transaction échouée
        }

        
        // if($stockItem->getFinalPayPrice() <= $user->getMoney()){
        //     // $stockItem = $this->worldRepository->findOneBy(['user' => $user, 'worldData' => $newWorldData]);
        //     $stockItem->setIsBought(true);
        //     $stockItem->setUser($user);
        //     $stockItem->setUserSellPrice($sellPrice);
        //     $stockItem->setBoughtAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
        //     $this->entityManager->persist($stockItem);

        //     $user->setMoney($user->getMoney() - $stockItem->getFinalPayPrice());
        //     $this->entityManager->persist($user);

        //     $this->entityManager->flush();
        // } else {
        //     // pas assez d'argent
        // }

    }

    
}