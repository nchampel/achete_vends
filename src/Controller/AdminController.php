<?php

namespace App\Controller;

use App\Repository\StockItemRepository;
use App\Repository\WorldDataRepository;
use App\Repository\WorldRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/stock/item/all', name: 'app_stock_item_all_index')]
    public function index(StockItemRepository $stockItemRepository): Response
    {
        return $this->render('admin/indexStockItem.html.twig', [
            // 'stock_items' => $stockItemRepository->findAll(),
            'stock_items_users_not_sold' => $stockItemRepository->findStockItemsOfAllUsersNotSold(),
            'stock_items_users_sold' => $stockItemRepository->findStockItemsOfAllUsersSold(),
            'stock_items_stock' => $stockItemRepository->findStockItemsOfUserNullBuyable(),
            //'user' => $this->getUser(),
        ]);
    }
    #[Route('/world/data/all', name: 'app_world_data_index_admin', methods: ['GET'])]
    public function indexWP(WorldDataRepository $worldDataRepository): Response
    {
        // if (!$this->getUser()) {
        //     return $this->redirectToRoute('app_login');
        // }
        return $this->render('world_data/index.html.twig', [
            'world_datas' => $worldDataRepository->findAll(),
        ]);
    }
    #[Route('/world/data/all', name: 'app_world_data_index_admin', methods: ['GET'])]
    public function indexWorldData(WorldDataRepository $worldDataRepository): Response
    {
        // if (!$this->getUser()) {
        //     return $this->redirectToRoute('app_login');
        // }
        return $this->render('world_data/index.html.twig', [
            'world_datas' => $worldDataRepository->findAll(),
        ]);
    }
    #[Route('/world/player/all', name: 'app_world_players_index', methods: ['GET'])]
    public function indexWorldPlayer(WorldRepository $worldRepository): Response
    {
        // if (!$this->getUser()) {
        //     return $this->redirectToRoute('app_login');
        // }
        return $this->render('admin/indexWorld.html.twig', [
            'worlds' => $worldRepository->findAll(),
            // 'user' => $this->getUser()
        ]);
    }
}
