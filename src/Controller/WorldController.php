<?php

namespace App\Controller;

use App\Entity\World;
use App\Form\WorldType;
use App\Repository\WorldRepository;
use App\Service\WorldService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('profile//world')]
class WorldController extends AbstractController
{
    #[Route('/', name: 'app_world_index', methods: ['GET'])]
    public function index(WorldRepository $worldRepository): Response
    {
        // if (!$this->getUser()) {
        //     return $this->redirectToRoute('app_login');
        // }
        return $this->render('world/index.html.twig', [
            'worlds' => $worldRepository->findAll(),
            'user' => $this->getUser()
        ]);
    }

    #[Route('/unlock', name: 'app_world_unlock', methods: ['GET'])]
    public function unlockWorld(WorldRepository $worldRepository, WorldService $service): Response
    {
        // if (!$this->getUser()) {
        //     return $this->redirectToRoute('app_login');
        // }
        $service->unlockNextWorld($this->getUser());

        $this->redirectToRoute('app_world_index');

        return $this->render('world/index.html.twig', [
            'worlds' => $worldRepository->findAll(),
            'user' => $this->getUser()
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'app_world_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $world = new World();
        $form = $this->createForm(WorldType::class, $world);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($world);
            $entityManager->flush();

            return $this->redirectToRoute('app_world_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('world/new.html.twig', [
            'world' => $world,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id<\d+>}', name: 'app_world_show', methods: ['GET'])]
    public function show(World $world): Response
    {
        return $this->render('world/show.html.twig', [
            'world' => $world,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id<\d+>}/edit', name: 'app_world_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, World $world, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(WorldType::class, $world);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_world_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('world/edit.html.twig', [
            'world' => $world,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id<\d+>}', name: 'app_world_delete', methods: ['POST'])]
    public function delete(Request $request, World $world, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$world->getId(), $request->request->get('_token'))) {
            $entityManager->remove($world);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_world_index', [], Response::HTTP_SEE_OTHER);
    }
}
