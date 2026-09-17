<?php

namespace App\Service;

use App\Entity\StockItem;
use App\Repository\ItemRepository;
use App\Repository\StockItemRepository;
use Doctrine\ORM\EntityManagerInterface;

class BuyAIService
{
    public function __construct(private EntityManagerInterface $entityManager, private StockItemRepository $stockItemRepository, private ItemRepository $itemRepository){
        $this->entityManager = $entityManager;
        $this->stockItemRepository = $stockItemRepository;
        $this->itemRepository = $itemRepository;
    }
    public function buy(): void
    {
        /** @var \App\Entity\StockItem[] $itemsOnPendingSale */
        $itemsOnPendingSale = $this->stockItemRepository->findStockItemsOfAllUsersNotSoldAndPendingSale();
        // return $itemsOnPendingSale;
        // foreach ($itemsOnPendingSale as $item) {
        //     echo $item->getId();
        // }
        echo(count($itemsOnPendingSale));
            
        foreach ($itemsOnPendingSale as $stockItem) {

            if(!$stockItem->isBought() || is_null($stockItem->getUser()) || $stockItem->isSold()){
                echo "erreur dans les paramètres pour l'achat par l'iA";
            }
            /**  @var \App\Entity\StockItem $stockItem */
            $userSellPrice = $stockItem->getUserSellPrice();
            $sellFinalPrice = $stockItem->getFinalSellPrice();
            $percent = 0;

            // $userSellPrice = 100;
            // $sellPrice = 100;

            // on calcule le % positif ou négatif par rapport au prix conseillé
            $differencePercent = ($userSellPrice - $sellFinalPrice) * 100 / $sellFinalPrice;
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
                            
            if($userSellPrice == $sellFinalPrice){
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

            // dump($sellNumber);

            $user = $stockItem->getUser();

            if($percent > $sellNumber){
                $stockItem->setIsSold(true);
                $stockItem->setSoldAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
                $user->setMoney($user->getMoney() + $stockItem->getUserSellPrice());
                $this->entityManager->persist($stockItem);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                echo "transaction réussie";
            } else {
                // transaction échouée
                echo "transaction échouée";
            }
        }
        echo "transactions réussies";
            
            // $this->entityManager->flush();
            // $this->addFlash('success', "Les nouveaux articles ont été générés");
            // echo("Les nouveaux articles ont été générés");
        }
    }