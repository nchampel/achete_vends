<?php

namespace App\Controller;

use App\Entity\StockItem;
use App\Repository\ItemRepository;
use App\Repository\StockItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CronController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private StockItemRepository $stockItemRepository,
        // private ItemService $itemService
        // private WorldRepository $worldRepository,
    ) {
        // $this->itemService = $itemService;
        $this->stockItemRepository = $stockItemRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/generate/stock/item/{token}', name: 'app_stock_item_generate', methods: ['GET'])]
    public function generateCron(ItemRepository $itemRepository, Request $request, string $token): Response
    {
        // if ($this->appService->getConfig('maintenance') == "true") {
        //     return $this->redirectToRoute('app_maintenance');
        // }
        $referer = $request->headers->get('referer');
        if ($token == $_ENV['APP_TOKEN_APP']) {
        // on rend inachetable ceux qui n'ont pas été achetés
        /** @var \App\Entity\StockItem[] $outdatedItems */
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
            $itemModels = $itemRepository->findAll();
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
            $this->addFlash('success', "Les nouveaux articles ont été générés");
        }
        return $this->redirect($referer ?? $this->generateUrl('app_stock_item_index'));
        return $this->render('stock_item/index.html.twig', [
            'stock_items' => $this->stockItemRepository->findAll(),
        ]);
    }
}
