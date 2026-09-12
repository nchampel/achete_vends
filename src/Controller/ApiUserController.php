<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiUserController extends AbstractController
{
    // #[Route('/api/user', name: 'app_api_user')]
    // public function index(): Response
    // {
    //     return $this->render('api_user/index.html.twig', [
    //         'controller_name' => 'ApiUserController',
    //     ]);
    // }
    #[Route('/me', name: 'api_me')]
    public function me(): Response|JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        
        // if(!($user)){
        //     return $this->redirectToRoute('app_login');
        // }
        $userData = $user->getProfileData();

        return $this->json([
                // 'message' => 'JWT valide !',
                'user' => $userData,
            ]);

        // $jwt= "token";

        // if($jwt == "token"){
        //     return $this->json([
        //         // 'message' => 'JWT valide !',
        //         'user' => $userData,
        //     ]);
        // } else {

        //     return $this->render('home/index.html.twig', [
        //         // 'controller_name' => 'HomeController',
        //         'user' => $userData,
        //     ]);
        // }

    }
}
