<?php

namespace App\Controller;

use App\Repository\StockItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/stock/item/all', name: 'app_stock_item_all_index')]
    public function index(StockItemRepository $stockItemRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            // 'stock_items' => $stockItemRepository->findAll(),
            'stock_items_users_not_sold' => $stockItemRepository->findStockItemsOfAllUsersNotSold(),
            'stock_items_users_sold' => $stockItemRepository->findStockItemsOfAllUsersSold(),
            'stock_items_stock' => $stockItemRepository->findStockItemsOfUserNullBuyable(),
            //'user' => $this->getUser(),
        ]);
    }
}
