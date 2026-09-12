<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/api/test', name: 'app_api_test')]
    public function test(): Response
    {
        return $this->json([
        'message' => 'JWT valide !',
        // 'user' => $this->getUser()->getUserIdentifier(),
        'data' => $this->getUser()->getProfileData(),
        // 'data_av' => $this->getUser()
    ]);
    }
}
