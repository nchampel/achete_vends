<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiGPSController extends AbstractController
{
    #[Route('/api/gps', name: 'app_api_gps')]
    public function index(): Response
    {
        // return $this->json([
        //     [
        //         "id" => 1,
        //         "latitude" => 43.5297,
        //         "longitude" => 5.4474,
        //         "activationRadius" => 50,
        //         "imageUrl" => "https://...",
        //         "type" => "wood"
        //         ],
        //     ]);
        
    return $this->json([
        [
        "id" => 1,
        "latitude" => 43.52975,
        "longitude" => 5.44740,
        "activationRadius" => 3,
        "type" => "wood"
    ],
    [
        "id" => 2,
        "latitude" => 43.52985,
        "longitude" => 5.44745,
        "activationRadius" => 3,
        "type" => "water"
    ],
        [
        "id" => 3,
        "latitude" => 37.4219983,
        "longitude" => -122.08410,
        "activationRadius" => 3,
        "type" => "wood"
    ],
    [
        "id" => 4,
        "latitude" => 37.4219987,
        "longitude" => -122.08390,
        "activationRadius" => 3,
        "type" => "water"
    ],
        [
        "id" => 5,
        "latitude" => 43.4237600,
        "longitude" => 5.2875000,
        "activationRadius" => 3,
        "type" => "wood"
    ],
    [
        "id" => 6,
        "latitude" => 43.4237382,
        "longitude" => 5.2873261,
        "activationRadius" => 3,
        "type" => "water"
    ],
    ]);
        
    }
}