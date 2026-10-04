<?php

namespace App\Controller;

use App\Entity\Building;
use App\Entity\Resource;
use App\Enum\BuildingType;
use App\Repository\BuildingRepository;
use App\Repository\ResourceRepository;
use App\Repository\ResourceStockRepository;
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

    private function determinateResourceIsCollectable(Resource $resource)
    {
        // $now = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        // echo  (string) $now->format('Y-m-d H:i:s');
        if($resource->getPeremption() > new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'))){
            return false;
        } else {
            // $resource->setIsCollectable(false);
            // $this->entityManager->persist($resource);
            // $this->entityManager->flush();
            return true;
        }
    }

    #[Route('/gps/resources/get', name: 'app_api_gps_resources_get', methods: ["POST"])]
    public function index(ResourceRepository $resourceRepository, Request $request): Response
    {
        // $playerPosition = ["latitude" => 43.4237596, "longitude" => 5.2876443];
        $data = json_decode($request->getContent(), true);
        $latitudeData = $data['latitude'] ?? null;
        $longitudeData = $data['longitude'] ?? null;
        $playerPosition = ["latitude" => $latitudeData, "longitude" => $longitudeData];
        // on récupère les ressources récoltables et on vérifie si elles sont pas périmées
        $recoltableResources = [];
        $resources = $resourceRepository->findResourcesAroundPlayer($playerPosition);
        $countOutdatedResources = 0;

        foreach($resources as $resource){
            $isOutdated = $this->determinateResourceIsCollectable($resource);
            if($isOutdated){
                $countOutdatedResources++;
                $resource->setIsCollectable(false);
                $this->entityManager->persist($resource);
            } else {
                $recoltableResources[] = $resource;
            }
        }
        if($countOutdatedResources > 0){
            $this->entityManager->flush();
        }

        return $this->json([
                'message' => "Ressources récupérées",
                    'resources' => $recoltableResources,
                ], 200, [], [
                    'groups' => [
                        'resource:read',
                    ],
            ]);
        
    

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
        "activationRadius" => 15,
        "type" => "wood"
    ],
    [
        "id" => 2,
        "latitude" => 43.52985,
        "longitude" => 5.44745,
        "activationRadius" => 15,
        "type" => "water"
    ],
        [
        "id" => 15,
        "latitude" => 37.4219983,
        "longitude" => -122.08410,
        "activationRadius" => 15,
        "type" => "wood"
    ],
    [
        "id" => 4,
        "latitude" => 37.4219987,
        "longitude" => -122.08390,
        "activationRadius" => 15,
        "type" => "water"
    ],
        [
        "id" => 5,
        "latitude" => 43.4237600,
        "longitude" => 5.2875000,
        "activationRadius" => 15,
        "type" => "wood"
    ],
    [
        "id" => 6,
        "latitude" => 43.4237682,
        "longitude" => 5.2873261,
        "activationRadius" => 15,
        "type" => "water"
    ],
        [
        "id" => 7,
        "latitude" => 37.4220295,
        "longitude" => -122.08410,
        "activationRadius" => 15,
        "type" => "treasure"
    ],
    [
        "id" => 8,
        "latitude" => 37.4219815,
        "longitude" => -122.08370,
        "activationRadius" => 15,
        "type" => "metal"
    ],
    // [
    //     "id" => 9,
    //     "latitude" => 43.4237402,
    //     "longitude" => 5.2875569,
    //     "activationRadius" => 15,
    //     "type" => "water"
    // ],
    [
        "id" => 10,
        "latitude" => 43.4237402,
        "longitude" => 5.2875669,
        "activationRadius" => 15,
        "type" => "water"
    ],
    ]);
        
    }

    #[Route('/gps/resource/collect/{id<\d+>}', name: 'app_api_gps_resource_collect', methods: ['POST'])]
    public function collect(Resource $resource, ResourceStockRepository $resourceStockRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $name = $data['name'] ?? null;
        $quantity = $resource->getFinalQuantity();
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $resourceStock = $resourceStockRepository->findByUserAndName($user, $name);
        $finalQuantity = $resourceStock->getQuantity() + $quantity;

        $resourceStock->setQuantity($finalQuantity);
        $entityManager->persist($resourceStock);
        $entityManager->flush();

        // $user->getResourceStocks();

        return $this->json(
            [
                'collected' => true,
                'resourceStock' => $resourceStock,
            ],
            Response::HTTP_OK,
            [],
            [
                'groups' => ['resource_stock:read'],
            ]
        );
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
        //         // "activationRadius" => 15,
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
        // "activationRadius" => 15,
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
                // "activationRadius" => 15,
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