<?php

namespace App\Controller;

use App\Entity\Building;
use App\Enum\BuildingType;
use App\Repository\BuildingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiGPSController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        // private StockItemRepository $stockItemRepository,
        // private ItemService $itemService,
        // private WorldRepository $worldRepository,
        // private NormalizerInterface $serializer,
    ) {
        // $this->itemService = $itemService;
        // $this->stockItemRepository = $stockItemRepository;
        // $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    #[Route('/gps', name: 'app_api_gps')]
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
        "latitude" => 43.4237682,
        "longitude" => 5.2873261,
        "activationRadius" => 3,
        "type" => "water"
    ],
        [
        "id" => 7,
        "latitude" => 37.4220295,
        "longitude" => -122.08410,
        "activationRadius" => 3,
        "type" => "treasure"
    ],
    [
        "id" => 8,
        "latitude" => 37.4219815,
        "longitude" => -122.08370,
        "activationRadius" => 3,
        "type" => "metal"
    ],
    [
        "id" => 9,
        "latitude" => 43.4237402,
        "longitude" => 5.2876969,
        "activationRadius" => 3,
        "type" => "water"
    ],
    ]);
        
    }

    #[Route('/gps/building/position/save', name: 'app_api_gps_building_position_save', methods: ["POST"])]
    public function saveBuildingPosition(Request $request, BuildingRepository $buildingRepository): Response
    {
        // Récupération du JSON envoyé par Flutter
        $data = json_decode($request->getContent(), true);
        $typeData = $data['type'] ?? null;
        $latitudeData = $data['latitude'] ?? null;
        $longitudeData = $data['longitude'] ?? null;

        

        // Vérification du type reçu
        if ($typeData === null) {
            return $this->json([
                'error' => 'Le champ "type" est obligatoire.'
            ], Response::HTTP_BAD_REQUEST);
        }
        if ($latitudeData === null) {
            return $this->json([
                'error' => 'Le champ "latitude" est obligatoire.'
            ], Response::HTTP_BAD_REQUEST);
        }
        if ($longitudeData === null) {
            return $this->json([
                'error' => 'Le champ "longitude" est obligatoire.'
            ], Response::HTTP_BAD_REQUEST);
        }
        $typeData = $data["type"];
        if ($typeData === null || !defined(BuildingType::class . '::' . $typeData)) {
            return $this->json([
                'error' => 'Type de bâtiment invalide'
            ], 400);
        }
        $type = constant(BuildingType::class . '::' . $typeData);

        // echo $type;

        $user = $this->getUser();

        // echo $type;

        // on vérifie s'il n'y a pas déjà un bâtiment de ce type
        $buildingCheck = $buildingRepository->findOneBy(["user" => $user, "name" => $type]);

        // Si déjà présent, on retourne simplement celui qui existe
        if ($buildingCheck !== null) {
            return $this->json(
                [
                    'created' => false,
                    'building' => $buildingCheck,
                ],
                Response::HTTP_OK,
                [],
                [
                    'groups' => ['building:read'],
                ]
            );
        }

        // echo($buildingCheck);

        // if(!is_null($buildingCheck)){
        //     return $this->json(
        //         [
        //         "type" => "déjà enregistré",
        //         // "latitude" => 43.52975,
        //         // "longitude" => 5.44740,
        //         // "activationRadius" => 3,
        //         // "type" => "wood"
        //     ]);
        // }

        // dump($user->getId());

        $building = new Building();
        $building->setName($type);
        $building->setUser($user);
        $building->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
        $building->setLatitude($latitudeData);
        $building->setLongitude($longitudeData);

        $this->entityManager->persist($building);

        $this->entityManager->flush();
        // dump($type);
        
    return $this->json(
        [
        // "type" => $type,
        // "latitude" => 43.52975,
        // "longitude" => 5.44740,
        // "activationRadius" => 3,
        // "type" => "wood"
        'created' => true,
        'building' => $building,
                ], 200, [], [
                    'groups' => [
                        'building:read',
                    ],
    ],
    
    );
        
    }

    #[Route('/gps/buildings/position/get', name: 'app_api_gps_buildings_position_get', methods: ["GET"])]
    public function getBuildingsPosition(Request $request, BuildingRepository $buildingRepository): Response
    {
        

        $user = $this->getUser();

//         error_log('=== GET BUILDINGS ===');
// error_log('USER ID = ' . ($user?->getId() ?? 'NULL'));


        // on vérifie s'il n'y a pas déjà un bâtiment de ce type
        $buildings = $buildingRepository->findBy(["user" => $user]);

        // echo($buildingCheck);
        // error_log('BUILDINGS COUNT = ' . count($buildings));

        if(count($buildings) == 0){
            return $this->json(
                [
                "buildings" => "pas de bâtiments construit",
                // "latitude" => 43.52975,
                // "longitude" => 5.44740,
                // "activationRadius" => 3,
                // "type" => "wood"
            ]);
        }

        
        // dump($type);
        
    return $this->json([
                'message' => "bâtiments récupérés",
                    'buildings' => $buildings,
                ], 200, [], [
                    'groups' => [
                        'building:read',
                    ],
            ]);
        
    }
}