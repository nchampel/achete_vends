<?php

namespace App\Controller;

use App\Entity\WorldData;
use App\Form\WorldDataType;
use App\Repository\WorldDataRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/world/data')]
class WorldDataController extends AbstractController
{
    #[Route('/', name: 'app_world_data_index', methods: ['GET'])]
    public function index(WorldDataRepository $worldDataRepository): Response
    {
        return $this->render('world_data/index.html.twig', [
            'world_datas' => $worldDataRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_world_data_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $worldDatum = new WorldData();
        $form = $this->createForm(WorldDataType::class, $worldDatum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($worldDatum);
            $entityManager->flush();

            return $this->redirectToRoute('app_world_data_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('world_data/new.html.twig', [
            'world_datum' => $worldDatum,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_world_data_show', methods: ['GET'])]
    public function show(WorldData $worldDatum): Response
    {
        return $this->render('world_data/show.html.twig', [
            'world_datum' => $worldDatum,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_world_data_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, WorldData $worldDatum, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(WorldDataType::class, $worldDatum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_world_data_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('world_data/edit.html.twig', [
            'world_datum' => $worldDatum,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_world_data_delete', methods: ['POST'])]
    public function delete(Request $request, WorldData $worldDatum, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$worldDatum->getId(), $request->request->get('_token'))) {
            $entityManager->remove($worldDatum);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_world_data_index', [], Response::HTTP_SEE_OTHER);
    }
}
