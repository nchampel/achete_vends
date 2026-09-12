<?php

namespace App\Controller;

use App\Entity\StockItem;
use App\Form\StockItemPriceType;
use App\Service\ItemService;
use App\Form\StockItemType;
use App\Repository\ItemRepository;
use App\Repository\StockItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/profile/stock/item')]
class StockItemController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private StockItemRepository $stockItemRepository,
        private ItemService $itemService
        // private WorldRepository $worldRepository,
    ) {
        $this->itemService = $itemService;
        $this->stockItemRepository = $stockItemRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_stock_item_index', methods: ['GET'])]
    public function index(): Response
    {
        // if (!$this->getUser()) {
        //     return $this->redirectToRoute('app_login');
        // }
        return $this->render('stock_item/index.html.twig', [
            // 'stock_items' => $stockItemRepository->findAll(),
            'stock_items_user' => $this->stockItemRepository->findStockItemsOfUserNotSold($this->getUser()),
            'stock_items_stock' => $this->stockItemRepository->findStockItemsOfUserNullBuyable(),
            'user' => $this->getUser(),
        ]);
    }
    

    #[Route('/sold', name: 'app_stock_item_sold', methods: ['GET'])]
    public function sold(): Response
    {
        // if (!$this->getUser()) {
        //     return $this->redirectToRoute('app_login');
        // }
        return $this->render('stock_item/sold.html.twig', [
            // 'stock_items' => $stockItemRepository->findAll(),
            'stock_items_user' => $this->stockItemRepository->findStockItemsOfUserSold($this->getUser()),
            // 'stock_items_stock' => $stockItemRepository->findStockItemsOfUserNullBuyable(),
            'user' => $this->getUser(),
        ]);
    }

    
    #[Route('/sell/{id<\d+>}', name: 'app_stock_item_sell', methods: ['GET'])]
    public function sell(StockItem $stockItem): Response
    {
        // if (!$this->getUser()) {
        //     return $this->redirectToRoute('app_login');
        // }

        // dump($stockItem);
        $this->itemService->sellItem($this->getUser(), $stockItem);
        // die();

        return $this->render('stock_item/index.html.twig', [
            // 'stock_items' => $stockItemRepository->findAll(),
            'stock_items_user' => $this->stockItemRepository->findStockItemsOfUserNotSold($this->getUser()),
            'stock_items_stock' => $this->stockItemRepository->findStockItemsOfUserNullBuyable(),
            'user' => $this->getUser(),
        ]);
    }

     #[Route('/{id<\d+>}/price/edit', name: 'app_stock_item_price_edit', methods: ['GET', 'POST'])]
    public function editPrice(Request $request, StockItem $stockItem): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        // if (!$user) {
        //     return $this->redirectToRoute('app_login');
        // }
        if($stockItem->getId() != $user->getId()){
            return $this->redirectToRoute('app_stock_item_index');
        }
        $form = $this->createForm(StockItemPriceType::class, $stockItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('app_stock_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stock_item/editPrice.html.twig', [
            'stock_item' => $stockItem,
            'form' => $form,
        ]);
    }
     #[Route('/{id<\d+>}/buy', name: 'app_stock_item_buy', methods: ['GET', 'POST'])]
    public function buy(StockItem $stockItem): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        // if (!$user) {
        //     return $this->redirectToRoute('app_login');
        // }
        if($stockItem->getId() != $user->getId()){
            return $this->redirectToRoute('app_stock_item_index');
        }
        $this->itemService->buyItem($user, $stockItem);

        return $this->renderForm('stock_item/index.html.twig', [
            'stock_items_user' => $this->stockItemRepository->findStockItemsOfUserNotSold($this->getUser()),
            'stock_items_stock' => $this->stockItemRepository->findStockItemsOfUserNullBuyable(),
            'user' => $this->getUser(),
        ]);
    }

    


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'app_stock_item_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $stockItem = new StockItem();
        $form = $this->createForm(StockItemType::class, $stockItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($stockItem);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_stock_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stock_item/new.html.twig', [
            'stock_item' => $stockItem,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_stock_item_show', methods: ['GET'])]
    public function show(StockItem $stockItem): Response
    {
        return $this->render('stock_item/show.html.twig', [
            'stock_item' => $stockItem,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'app_stock_item_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, StockItem $stockItem): Response
    {
        $form = $this->createForm(StockItemType::class, $stockItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('app_stock_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stock_item/edit.html.twig', [
            'stock_item' => $stockItem,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_stock_item_delete', methods: ['POST'])]
    public function delete(Request $request, StockItem $stockItem): Response
    {
        if ($this->isCsrfTokenValid('delete'.$stockItem->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($stockItem);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_stock_item_index', [], Response::HTTP_SEE_OTHER);
    }
}
