<?php

namespace App\Controller;

use App\Repository\StockItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiStockItemController extends AbstractController
{
    public function __construct(
        // private EntityManagerInterface $entityManager,
        private StockItemRepository $stockItemRepository,
        // private ItemService $itemService
        // private WorldRepository $worldRepository,
    ) {
        // $this->itemService = $itemService;
        $this->stockItemRepository = $stockItemRepository;
        // $this->entityManager = $entityManager;
    }

    #[Route('/all', name: 'api_stock_item_stock', methods: ['GET'])]
    public function stock(): Response|JsonResponse
    {
        // if (!$this->getUser()) {
        //     return $this->redirectToRoute('app_login');
        // }

        /** @var \App\Entity\StockItem[] $stockItems*/
            $stockItems = $this->stockItemRepository->findStockItemsOfUserNullBuyable();
            $stockItemsData = [];
            foreach ($stockItems as $si){
                $stockItemsData[] = ["final_pay_price" => $si->getFinalPayPrice(), "name" => $si->getItem()->getName(), "id" => $si->getId()];
            }
            return $this->json([
                // 'message' => 'JWT valide !',
                'stock_items_stock' => $stockItemsData,
            ]);

        // $jwt= "token";

        // if($jwt == "token"){
        //     /** @var \App\Entity\StockItem[] $stockItems*/
        //     $stockItems = $this->stockItemRepository->findStockItemsOfUserNullBuyable();
        //     $stockItemsData = [];
        //     foreach ($stockItems as $si){
        //         $stockItemsData[] = ["final_pay_price" => $si->getFinalPayPrice(), "name" => $si->getItem()->getName(), "id" => $si->getId()];
        //     }
        //     return $this->json([
        //         // 'message' => 'JWT valide !',
        //         'stock_items_stock' => $stockItemsData,
        //     ]);
        // } else {

        //     return $this->render('home/index.html.twig', [
        //         // 'controller_name' => 'HomeController',
        //         'stock_items_stock' => $this->stockItemRepository->findStockItemsOfUserNullBuyable(),
        //     ]);
        // }
    }
}
