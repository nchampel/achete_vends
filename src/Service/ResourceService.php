<?php

namespace App\Service;

use App\Entity\Resource;
use App\Entity\StockItem;
use App\Repository\ItemRepository;
use App\Repository\ResourceRepository;
use App\Repository\StockItemRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;

class ResourceService
{
    public function __construct(private EntityManagerInterface $entityManager, private ResourceRepository $resourceRepository, private ItemRepository $itemRepository)
    {
        $this->entityManager = $entityManager;
        $this->resourceRepository = $resourceRepository;
        $this->itemRepository = $itemRepository;
    }

    private function addSeconds(DateTimeInterface $date, int $seconds): DateTimeImmutable
    {
        $copy = DateTimeImmutable::createFromInterface($date);

        return $copy->modify(sprintf('%+d seconds', $seconds));
    }
    public function repop(): void
    {
        // on a la ressource périmée qui est isCollectable false et isCollected false
        // on a la ressource récoltée qui est isCollectable false et isCollected true
        // on a la ressource non récoltée non périmée qui est isCollectable true et isCollected false

        // en profiter pour rendre isCollectable false si ressource périmée
        // $collectableResources = $this->resourceRepository->findBy(["isCollectable" => true]);
        $countOutdatedResources = 0;
        $countResourcesRepopped = 0;

        $resources = $this->resourceRepository->findAll();

        $now = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));


        foreach ($resources as $resource) {
            // on rend isCollectable false si ressource périmée
            
            // 1. Extraire la date/heure brute sans l'offset/timezone
            $peremptionDateString = $resource->getPeremption()->format("Y-m-d H:i:s.u");

            // 2. Créer l'objet directement dans le bon timezone
            $peremptionDate = new \DateTimeImmutable($peremptionDateString, new \DateTimeZone('Europe/Paris'));
            $repopDateOutDated = $this->addSeconds($peremptionDate, $resource->getFinalRepopTime());

            if ($resource->isCollected()) {
                // 1. Extraire la date/heure brute sans l'offset/timezone
                $collectedAtDateString = $resource->getCollectedAt()->format("Y-m-d H:i:s.u");

                // 2. Créer l'objet directement dans le bon timezone
                $collectedAt = new \DateTimeImmutable($collectedAtDateString, new \DateTimeZone('Europe/Paris'));
                $repopDate = $this->addSeconds($collectedAt, $resource->getFinalRepopTime());
                // ressource collectée repopable ou pas non périmée
                if ($repopDate <= $now) {
                    // ressource collectée et repopable
                    $countResourcesRepopped++;
                    $this->repopResource($resource, $now);
                } else {
                    // ressource collectée et non repopable
                    // on ne fait rien
                }
            } else {

                $isOutdated = $this->determinateResourceIsCollectable($resource, $now);
                if ($isOutdated && $repopDateOutDated > $now) {
                    // ressource non récoltée mais périmée et non repopable
                    $countOutdatedResources++;
                    $resource->setIsCollectable(false);
                    $this->entityManager->persist($resource);
                } else if ($isOutdated && $repopDateOutDated <= $now) {
                    // ressource non récoltée mais périmée et repopable
                    $countResourcesRepopped++;
                    $this->repopResource($resource, $now);
                } else {
                    // ressource non récoltée et non périmée
                    // on ne fait rien car récoltable
                }
            }
        }


        // certainement filtrer resource périmé (isCollectable false et/ou date péremption < now) par date premeption + final repop par rapport au moment actuel, changer date peremption, repasser isColelctable et is colelcted à true
        // mais aussi filtrer par ceux non périmés mais récolté,  date premeption + final repop par rapport au moment actuel, changer date peremption  repasser isColelctable et is colelcted à true
        



        
        if ($countResourcesRepopped > 0 || $countOutdatedResources > 0) {
            $this->entityManager->flush();
        }
        echo "repop réussi à " . $now->format("d-m-Y H:i:s") . " " . $countResourcesRepopped . " resource(s) repopées";

        // $this->entityManager->flush();
        // $this->addFlash('success', "Les nouveaux articles ont été générés");
        // echo("Les nouveaux articles ont été générés");
    }

    public function determinateResourceIsCollectable(Resource $resource, DateTimeImmutable $now)
    {
        // 1. Extraire la date/heure brute sans l'offset/timezone
        $rawDateString = $resource->getPeremption()->format("Y-m-d H:i:s.u");

        // 2. Créer l'objet directement dans le bon timezone
        $peremption = new \DateTimeImmutable($rawDateString, new \DateTimeZone('Europe/Paris'));
        // $now = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        // echo  (string) $now->format('Y-m-d H:i:s');
        // if($peremption > new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'))){
        if ($peremption > $now) {
            return false;
        } else {
            // $resource->setIsCollectable(false);
            // $this->entityManager->persist($resource);
            // $this->entityManager->flush();
            return true;
        }
    }

    private function repopResource(Resource $resource, DateTimeImmutable $now)
    {
        $resource->setCollectedAt(null);
        $resource->setIsCollectable(true);
        $resource->setIsCollected(false);
        $resource->setPeremption($this->addSeconds($now, $resource->getResource()->getCollectableTime()));
        $resourceNumberAlea = random_int(1, 7);
        switch($resourceNumberAlea){
            case 1:
            case 2:
            case 3:
            case 4:
                $resourceNumber = 1;
                break;
            case 5:
            case 6:
                $resourceNumber = 2;
                break;
            case 7:
                $resourceNumber = 3;
                break;
            default:
                $resourceNumber = 1;
                break;
        }
        $resource->setFinalQuantity($resource->getResource()->getQuantity() * $resourceNumber);
        $this->entityManager->persist($resource);
    }
}
