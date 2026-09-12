<?php

namespace App\Service;

use App\Entity\StockItem;
use App\Repository\ItemRepository;
use App\Repository\StockItemRepository;
use Doctrine\ORM\EntityManagerInterface;

class StockGeneratorService
{
    public function __construct(private EntityManagerInterface $entityManager, private StockItemRepository $stockItemRepository, private ItemRepository $itemRepository){
        $this->entityManager = $entityManager;
        $this->stockItemRepository = $stockItemRepository;
        $this->itemRepository = $itemRepository;
    }
    public function generate(): void
    {
        $outdatedItems = $this->stockItemRepository->findBy(['user' => null]);
            foreach ($outdatedItems as $item) {

            $item->setIsBuyable(false);
            $this->entityManager->persist($item);
                // $entityManager->remove($item);
                // $entityManager->flush();
            }

            $improvedQuantity = 1;
            // récupérer ici la valeur de la quantité améliorée

            /** @var \App\Entity\Item[] $itemModels */
            $itemModels = $this->itemRepository->findAll();
            foreach ($itemModels as $model) {
                // for($j = 0; $j <= 3; $j++){
                    $quantity = random_int(1, 3) * $improvedQuantity;
                    $payPriceModel = $model->getPayPrice();
                    $sellPriceModel = $model->getSellPrice();
                    for($i = 1; $i <= $quantity; $i++){
                        $stockItemGenerated = new StockItem();
                        $stockItemGenerated->setItem($model);
                        $stockItemGenerated->setUser(null);
                        $stockItemGenerated->setIsBought(false);
                        $stockItemGenerated->setIsBuyable(true);
                        $stockItemGenerated->setIsSold(false);
                        $stockItemGenerated->setUserSellPrice(0);
                        // $priceModel = $model->getPrice();
                        // $cooldownModel = $model->getCooldown();
                        // $stockItemGenerated->setFinalPayPrice(random_int(round($payPriceModel * 0.8, 2), round($payPriceModel * 1.2, 2)));
                        // $stockItemGenerated->setFinalSellPrice(random_int(round($sellPriceModel * 0.8, 2), round($sellPriceModel * 1.2, 2)));
                        $minPayPrice = (int) round($payPriceModel * 0.8 * 100);
                        $maxPayPrice = (int) round($payPriceModel * 1.2 * 100);

                        $minSellPrice = (int) round($sellPriceModel * 0.8 * 100);
                        $maxSellPrice = (int) round($sellPriceModel * 1.2 * 100);

                        $stockItemGenerated->setFinalPayPrice(
                            random_int($minPayPrice, $maxPayPrice) / 100
                        );

                        $stockItemGenerated->setFinalSellPrice(
                            random_int($minSellPrice, $maxSellPrice) / 100
                        );
                        // $stockItemGenerated->setstockitem($model);
                        $stockItemGenerated->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
                        // $forestResourceGenerated->setType("");
                        $this->entityManager->persist($stockItemGenerated);
                    // }
                }
            }
            $this->entityManager->flush();
            // $this->addFlash('success', "Les nouveaux articles ont été générés");
            // echo("Les nouveaux articles ont été générés");
        }
    }