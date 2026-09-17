<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Item;
use App\Entity\StockItem;
use App\Repository\ItemRepository;
use App\Repository\StockItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

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
     * @return string
     *         achat d'un article.
     */
    public function buyItem(User $user, StockItem $stockItem): string
    {
        // stockitem null ou pas achetable
        $this->entityManager->beginTransaction();

        try {
            /**  @var \App\Entity\StockItem $stockItem */
            $stockItem = $this->stockItemRepository->findForUpdate($stockItem->getId());

            if ($stockItem === null) {
                throw new \RuntimeException('Article introuvable');
            }

            // On vérifie l'état APRÈS avoir obtenu le verrou.
        if (
            $stockItem->getUser() !== null
            || !$stockItem->isBuyable()
            || $stockItem->isBought()
        ) {
            throw new \RuntimeException(
                'Article déjà acheté ou indisponible ou null'
            );
        }

        if ($stockItem->getFinalPayPrice() > $user->getMoney()) {
            throw new \RuntimeException(
                "Pas assez d'argent"
            );
        }


        // $newWorldData = $this->stockItemRepository->findOneBy(['id' => $newWorldNumber]);
        
            // $stockItem = $this->worldRepository->findOneBy(['user' => $user, 'worldData' => $newWorldData]);
            
        $stockItem->setIsBought(true);
        $stockItem->setUser($user);
        // $stockItem->setUserSellPrice($stockItem->getFinalSellPrice()); pas bon car on doit analyser avant
        $stockItem->setUserSellPrice($stockItem->getFinalPayPrice());
        $stockItem->setBoughtAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
        // on met le numéro d'achat, qui est incrémenté par la fonction du repo
        $number = $this->stockItemRepository->findMaxNumberByUser($user);
        $stockItem->setNumber($number + 1);
        $this->entityManager->persist($stockItem);

        $user->setMoney($user->getMoney() - $stockItem->getFinalPayPrice());
        $this->entityManager->persist($user);

        $this->entityManager->flush();
        // IMPORTANT
        $this->entityManager->commit();
        return "Achat effectué";


        } catch (\Throwable $e) {
            $this->entityManager->rollback();

            throw $e;
        }
        
    }
    /**
     * Analyse par le joueur d'un article.
     *
     * @return string
     *         analyse d'un article.
     */
    public function analyseItem(User $user, StockItem $stockItem): string
    {
        // stockitem null ou pas achetable
        $this->entityManager->beginTransaction();

        try {
            /**  @var \App\Entity\StockItem $stockItem */
            $stockItem = $this->stockItemRepository->findForUpdate($stockItem->getId());

            if ($stockItem === null) {
                throw new \RuntimeException('Article introuvable');
            }

            // On vérifie l'état APRÈS avoir obtenu le verrou.
        if (
            is_null($stockItem->getUser())
            || $stockItem->isSold()
            || !$stockItem->isBought()
            || $stockItem->isAnalysed()
        ) {
            throw new \RuntimeException(
                'Article déjà acheté ou vendu ou pas acheté => analyse'
            );
        }

        // if ($stockItem->getFinalPayPrice() > $user->getMoney()) {
        //     throw new \RuntimeException(
        //         "Pas assez d'argent"
        //     );
        // }


        // $newWorldData = $this->stockItemRepository->findOneBy(['id' => $newWorldNumber]);
        
            // $stockItem = $this->worldRepository->findOneBy(['user' => $user, 'worldData' => $newWorldData]);

            // sleep(5);

            // return "Analyse effectuée";
            
        $stockItem->setIsAnalysed(true);
        // $stockItem->setUserSellPrice($stockItem->getFinalSellPrice()); pas bon car on doit analyser avant
        $stockItem->setAnalysedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
        $this->entityManager->persist($stockItem);

        $this->entityManager->flush();
        $this->entityManager->commit();
        return "Analyse effectuée";
        } catch (\Throwable $e) {
            $this->entityManager->rollback();

            throw $e;
        }
        
    }
    /**
     * Fixation par le joueur du prix d'un article.
     *
     * @return string
     *         fixation du prix de vente.
     */
    public function setPriceItem(User $user, StockItem $stockItem, float $price): string
    {

        // $newWorldData = $this->stockItemRepository->findOneBy(['id' => $newWorldNumber]);
        /**  @var \App\Entity\StockItem $stockItem */
        
        $stockItem->setUserSellPrice($price);
        $this->entityManager->persist($stockItem);

        $this->entityManager->flush();
        return "Prix enregistré";
       
    }
    /**
     *Mise en vente par le joueur d'un article.
     *
     * @return void
     *         mise en vente au prix de vente fixé par le joueur.
     */
    public function sellItem(User $user, StockItem $stockItem): void
    {

    }
    /**
     *Action d'achat ou non par l'IA de l'article, prix mis par joueur.
     *
     * @return void
     *         
     */
    public function sellItemAI(User $user, StockItem $stockItem): string
    {

        // $newWorldData = $this->stockItemRepository->findOneBy(['id' => $newWorldNumber]);
        if(!$stockItem->isBought() || !$stockItem->getUser() || $stockItem->isSold()){
            return "erreur dans les paramètres";
        }
        /**  @var \App\Entity\StockItem $stockItem */
        $userSellPrice = $stockItem->getUserSellPrice();
        $sellPrice = $stockItem->getItem()->getSellPrice();
        $percent = 0;

        // $userSellPrice = 100;
        // $sellPrice = 100;

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

        // dump($sellNumber);

        if($percent > $sellNumber){
            $stockItem->setIsSold(true);
            $user->setMoney($user->getMoney() + $stockItem->getUserSellPrice());
            $this->entityManager->persist($stockItem);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return "transaction réussie";
        } else {
            // transaction échouée
            return "transaction échouée";
        }

        // $this->redire

        
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