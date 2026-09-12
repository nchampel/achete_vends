<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home_symfony')]
    public function home(): Response
    {
        return $this->redirectToRoute('app_login');
    }

    #[Route('/profile', name: 'app_home')]
    public function index(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // dump($user);

        // if(!($user)){
        //     return $this->redirectToRoute('app_login');
        // }

        return $this->render('home/index.html.twig', [
            // 'controller_name' => 'HomeController',
            // 'user' => $user->getProfileData(),
            'user' => $user
        ]);
    }
    
}
