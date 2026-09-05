<?php

namespace App\Controller;

use App\Entity\Constant;
use App\Form\ConstantType;
use App\Repository\ConstantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/constant')]
class ConstantController extends AbstractController
{
    #[Route('/', name: 'app_constant_index', methods: ['GET'])]
    public function index(ConstantRepository $constantRepository): Response
    {
        return $this->render('constant/index.html.twig', [
            'constants' => $constantRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_constant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $constant = new Constant();
        $form = $this->createForm(ConstantType::class, $constant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($constant);
            $entityManager->flush();

            return $this->redirectToRoute('app_constant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('constant/new.html.twig', [
            'constant' => $constant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_constant_show', methods: ['GET'])]
    public function show(Constant $constant): Response
    {
        return $this->render('constant/show.html.twig', [
            'constant' => $constant,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_constant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Constant $constant, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ConstantType::class, $constant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_constant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('constant/edit.html.twig', [
            'constant' => $constant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_constant_delete', methods: ['POST'])]
    public function delete(Request $request, Constant $constant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$constant->getId(), $request->request->get('_token'))) {
            $entityManager->remove($constant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_constant_index', [], Response::HTTP_SEE_OTHER);
    }
}
