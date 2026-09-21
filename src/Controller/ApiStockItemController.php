<?php

namespace App\Controller;

use App\Entity\StockItem;
use App\Repository\StockItemRepository;
use App\Service\ItemService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/api')]
class ApiStockItemController extends AbstractController
{
    public function __construct(
        // private EntityManagerInterface $entityManager,
        private StockItemRepository $stockItemRepository,
        private ItemService $itemService,
        // private WorldRepository $worldRepository,
        private NormalizerInterface $serializer,
    ) {
        $this->itemService = $itemService;
        $this->stockItemRepository = $stockItemRepository;
        $this->serializer = $serializer;
        // $this->entityManager = $entityManager;
    }

    #[Route('/stock/item/all', name: 'api_stock_item_stock', methods: ['GET'])]
    public function stock(): Response|JsonResponse
    {
        // if (!$this->getUser()) {
        //     return $this->redirectToRoute('app_login');
        // }

        /** @var \App\Entity\StockItem[] $stockItems*/
            $stockItems = $this->stockItemRepository->findStockItemsOfUserNullBuyable();
            $stockItemsData = [];
            foreach ($stockItems as $stockItem){
                $stockItemsData[] = $this->json([
                    // "final_pay_price" => $stockItem->getFinalPayPrice(), 
                    // "user_sell_price" => $stockItem->getUserSellPrice(), 
                    // "final_sell_price" => null, 
                    // // "final_sell_price" => $stockItem->getFinalSellPrice(), 
                    // "name" => $stockItem->getItem()->getName(), 
                    // "id" => $stockItem->getId(),
                    // "number" => $stockItem->getNumber(),
                    // 'isAnalysed' => $stockItem->isAnalysed(),
                    // 'isPendingSale' => $stockItem->isPendingSale(),
                    // 'isSold' => $stockItem->isSold(),

                    'stockItem' => $stockItem
                 ], 200, [], [
                    'groups' => [
                        'stockItem:read',
                    ]]);
                
            }
            // return $this->json([
            //     // 'message' => 'JWT valide !',
            //     'stock_items_stock' => $stockItemsData,
            // ]);
            return $this->json(
                $stockItems,
                200,
                [],
                [
                    'groups' => ['stockItem:read'],
                ]
            );

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

    #[Route('/stock/item/user/not/sold', name: 'api_stock_item_user_not_sold', methods: ['GET'])]
    public function stockUser(): Response|JsonResponse
    {
        // if (!$this->getUser()) {
        //     return $this->redirectToRoute('app_login');
        // }

        /** @var \App\Entity\StockItem[] $stockItems*/
            $stockItems = $this->stockItemRepository->findStockItemsOfAllUsersNotSold();
            $stockItemsData = [];
            $data = [];
            foreach ($stockItems as $stockItem) {
        // On utilise uniquement le groupe stockItem:read
        $stockItemData = $this->serializer->normalize(
            $stockItem,
            null,
            [
                'groups' => ['stockItem:read'],
            ]
        );

        // On ajoute finalSellPrice uniquement si l'item est analysé
        if ($stockItem->isAnalysed()) {
            $stockItemData['finalSellPrice'] = $stockItem->getFinalSellPrice();
        }

        $data[] = $stockItemData;
    }

    return $this->json([
        'stockItems' => $data,
    ]);
                



            return $this->json([
                // 'message' => 'JWT valide !',
                'stock_items_stock' => $stockItemsData,
            ]);

        
    }

    #[Route('/stock/item/{id<\d+>}/buy', name: 'app_stock_item_buy', methods: ['POST'])]
    public function buy(StockItem $stockItem): Response
    {
        // dump("acheter");
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        // if (!$user) {
        //     return $this->redirectToRoute('app_login');
        // }
        // if(!is_null($stockItem->getUser()) || !$stockItem->isBuyable() || $stockItem->isBought()){
        //     // attention, à adapter pour flutter et idem pour fixation du prix
        //     dump("stockitem null ou pas achetable");
        //     return $this->json([
        //         'message' => 'stockitem null ou pas achetable',
        //     ], 409);
        //     // return $this->redirectToRoute('app_stock_item_index');
        // }

        try {
            $result = $this->itemService->buyItem(
                $user,
                $stockItem
            );

            return $this->json([
                'message' => $result["message"],
                    'user' => $result['user'],
                    'stockItem' => $result['stockItem'],
                ], 200, [], [
                    'groups' => [
                        'user:read',
                        'stockItem:read',
                    ],
                // 'stockItem' => [
                //     "final_pay_price" => $stockItem->getFinalPayPrice(), 
                //     "user_sell_price" => $stockItem->getUserSellPrice(), 
                //     "final_sell_price" => $stockItem->getFinalSellPrice(), 
                //     'price' => $stockItem->getUserSellPrice(),
                //     "name" => $stockItem->getItem()->getName(), 
                //     'id' => $stockItem->getId(),
                //     'number' => $stockItem->getNumber(),
                //     'isAnalysed' => $stockItem->isAnalysed(),
                //     'isPendingSale' => $stockItem->isPendingSale(),
                //     'isSold' => $stockItem->isSold(),
                // ],
                // 'user' => $user->getProfileData(),
            ]);
        } catch (\RuntimeException $e) {
            return $this->json([
                'message' => $e->getMessage(),
            ], 409);
        }

        return $this->renderForm('stock_item/index.html.twig', [
            'stock_items_user' => $this->stockItemRepository->findStockItemsOfUserNotSold($this->getUser()),
            'stock_items_stock' => $this->stockItemRepository->findStockItemsOfUserNullBuyable(),
            'user' => $this->getUser(),
        ]);
    }

     #[Route('/stock/item/{id<\d+>}/analyse', name: 'app_stock_item_analyse', methods: ['GET'])]
    public function analyse(StockItem $stockItem): Response
    {
        // dump("acheter");
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        // if (!$user) {
        //     return $this->redirectToRoute('app_login');
        // }
        // if(!is_null($stockItem->getUser()) || !$stockItem->isBuyable() || $stockItem->isBought()){
        //     // attention, à adapter pour flutter et idem pour fixation du prix
        //     dump("stockitem null ou pas achetable");
        //     return $this->json([
        //         'message' => 'stockitem null ou pas achetable',
        //     ], 409);
        //     // return $this->redirectToRoute('app_stock_item_index');
        // }


        try {
            $message = $this->itemService->analyseItem(
                $user,
                $stockItem,
            );

            // $result = false;

            // if($message == "Analyse effectuée"){
            //     $result = true;
            // }

            $data = $this->serializer->normalize(
                $stockItem,
                null,
                ['groups' => ['stockItem:read']]
            );

            $data['finalSellPrice'] = $stockItem->getFinalSellPrice();

            return $this->json([
                'message' => $message,
                'stockItem' => $data,
            ], 200);

            return $this->json([
                'message' => $message,
                'finalSellPrice' => $stockItem->getFinalSellPrice(),
                // 'result' => $result,
                'stockItem' => $stockItem
                 ], 200, [], [
                    'groups' => [
                        'stockItem:read',
                    ]]);
        } catch (\RuntimeException $e) {
            return $this->json([
                'message' => $e->getMessage(),
                // 'result' => false,
            ], 409);
        }

    }

    #[Route('/stock/item/{id<\d+>}/sell', name: 'app_stock_item_sell', methods: ['POST'])]
    public function sell(StockItem $stockItem, Request $request): Response
    {
        // dump("acheter");
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        // if (!$user) {
        //     return $this->redirectToRoute('app_login');
        // }
        // if(!is_null($stockItem->getUser()) || !$stockItem->isBuyable() || $stockItem->isBought()){
        //     // attention, à adapter pour flutter et idem pour fixation du prix
        //     dump("stockitem null ou pas achetable");
        //     return $this->json([
        //         'message' => 'stockitem null ou pas achetable',
        //     ], 409);
        //     // return $this->redirectToRoute('app_stock_item_index');
        // }

        $data = $request->toArray();

        $userSellPrice = $data['userSellPrice'] ?? null;
        // return $this->json([
        //     'userSellPrice' => $userSellPrice
        // ]);

        try {
            $message = $this->itemService->sellItem(
                $user,
                $stockItem,
                $userSellPrice,
            );

            // $result = false;

            // if($message == "Analyse effectuée"){
            //     $result = true;
            // }

            $data = $this->serializer->normalize(
                $stockItem,
                null,
                ['groups' => ['stockItem:read']]
            );

            $data['finalSellPrice'] = $stockItem->getFinalSellPrice();

            return $this->json([
                'message' => $message,
                'stockItem' => $data,
            ], 200);

            return $this->json([
                'message' => $message,
                // 'result' => $result,
                'stockItem' => $stockItem
                 ], 200, [], [
                    'groups' => [
                        'stockItem:read',
                    ]]);
        } catch (\RuntimeException $e) {
            return $this->json([
                'message' => $e->getMessage(),
                // 'result' => false,
            ], 409);
        }

    }
}
