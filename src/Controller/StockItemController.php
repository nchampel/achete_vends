<?php

namespace App\Controller;

use App\Entity\StockItem;
use App\Service\ItemService;
use App\Form\StockItemType;
use App\Repository\ItemRepository;
use App\Repository\StockItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/stock/item')]
class StockItemController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private StockItemRepository $stockItemRepository,
        private ItemService $itemService
        // private WorldRepository $worldRepository,
    ) {
        $this->itemService = $itemService;
    }

    #[Route('/', name: 'app_stock_item_index', methods: ['GET'])]
    public function index(StockItemRepository $stockItemRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('stock_item/index.html.twig', [
            // 'stock_items' => $stockItemRepository->findAll(),
            'stock_items_user' => $stockItemRepository->findStockItemsOfUserNotSold($this->getUser()),
            'stock_items_stock' => $stockItemRepository->findStockItemsOfUserNullBuyable(),
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/sold', name: 'app_stock_item_sold', methods: ['GET'])]
    public function sold(StockItemRepository $stockItemRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('stock_item/sold.html.twig', [
            // 'stock_items' => $stockItemRepository->findAll(),
            'stock_items_user' => $stockItemRepository->findStockItemsOfUserSold($this->getUser()),
            // 'stock_items_stock' => $stockItemRepository->findStockItemsOfUserNullBuyable(),
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/sell/{id<\d+>}', name: 'app_stock_item_sell', methods: ['GET'])]
    public function sell(StockItemRepository $stockItemRepository, StockItem $stockItem): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        dump($stockItem);
        $this->itemService->sellItem($this->getUser(), $stockItem);
        // die();

        return $this->render('stock_item/index.html.twig', [
            'stock_items' => $stockItemRepository->findAll(),
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/generate/{token}', name: 'app_stock_item_generate', methods: ['GET'])]
    public function generateCron(StockItemRepository $stockItemRepository, ItemRepository $itemRepository, EntityManagerInterface $entityManager, Request $request, string $token): Response
    {
        // if ($this->appService->getConfig('maintenance') == "true") {
        //     return $this->redirectToRoute('app_maintenance');
        // }
        $referer = $request->headers->get('referer');
        if ($token == $_ENV['APP_TOKEN_APP']) {
        // on rend inachetable ceux qui n'ont pas été achetés
        /** @var \App\Entity\StockItem[] $outdatedItems */
        $outdatedItems = $stockItemRepository->findBy(['user' => null]);
            foreach ($outdatedItems as $item) {

            $item->setIsBuyable(false);
            $entityManager->persist($item);
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
                        $entityManager->persist($stockItemGenerated);
                    // }
                }
            }
            $entityManager->flush();
            $this->addFlash('success', "Les nouveaux articles ont été générés");
        }
        return $this->redirect($referer ?? $this->generateUrl('app_stock_item_index'));
        return $this->render('stock_item/index.html.twig', [
            'stock_items' => $stockItemRepository->findAll(),
        ]);
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'app_stock_item_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $stockItem = new StockItem();
        $form = $this->createForm(StockItemType::class, $stockItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($stockItem);
            $entityManager->flush();

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
    public function edit(Request $request, StockItem $stockItem, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StockItemType::class, $stockItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_stock_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stock_item/edit.html.twig', [
            'stock_item' => $stockItem,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_stock_item_delete', methods: ['POST'])]
    public function delete(Request $request, StockItem $stockItem, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$stockItem->getId(), $request->request->get('_token'))) {
            $entityManager->remove($stockItem);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_stock_item_index', [], Response::HTTP_SEE_OTHER);
    }
}
