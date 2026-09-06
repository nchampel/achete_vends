<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\World;
use App\Repository\WorldDataRepository;
use App\Repository\WorldRepository;
use Doctrine\ORM\EntityManagerInterface;

class WorldService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private WorldDataRepository $worldDataRepository,
        private WorldRepository $worldRepository,
    ) {
    }

    /**
     * Débloque le prochain monde du joueur.
     *
     * @return World|null
     *         Le nouveau monde si le déblocage a réussi,
     *         null sinon.
     */
    public function unlockNextWorld(User $user): void
    {

               
        $currentWorld = $user->getCurrentWorld();

        if (!$currentWorld) {
            return;
        }

        $newWorldNumber = $currentWorld->getWorldData()->getNumber() + 1;
        // $newWorldNumber = $user->getWorlds()[0]->getWorld()->getNumber() + 1;
        $newWorldData = $this->worldDataRepository->findOneBy(['number' => $newWorldNumber]);
        if($newWorldData->getAmount() <= $user->getMoney()){
            $newWorld = $this->worldRepository->findOneBy(['user' => $user, 'worldData' => $newWorldData]);
            $newWorld->setIsUnlocked(true);
            $this->entityManager->persist($newWorld);

            $user->setMoney($user->getMoney() - $newWorldData->getAmount());
            // $user->addWorld($newWorld);
            // Ajout du monde à la collection du joueur
            $user->addWorld($newWorld);
            // Le nouveau monde devient le monde courant
            $user->setCurrentWorld($newWorld);
            $this->entityManager->persist($user);

            $this->entityManager->flush();
        } else {
            // pas assez d'argent
        }

        // return $newWorld;

        // // Récupération du monde actuellement sélectionné
        // $currentWorld = $user->getCurrentWorld();

        // if (!$currentWorld) {
        //     return null;
        // }

        // // Récupération des données du monde actuel
        // $currentWorldData = $currentWorld->getWorldData();

        // if (!$currentWorldData) {
        //     return null;
        // }

        // // Numéro du prochain monde
        // $nextWorldNumber = $currentWorldData->getNumber() + 1;

        // // Recherche des données du prochain monde
        // $nextWorldData = $this->worldDataRepository->findOneBy([
        //     'number' => $nextWorldNumber,
        // ]);

        // // Il n'existe pas de monde suivant
        // if (!$nextWorldData) {
        //     return null;
        // }

        // // Prix du prochain monde
        // $price = $nextWorldData->getAmount();

        // // Le joueur n'a pas assez d'argent
        // if ($user->getMoney() < $price) {
        //     return null;
        // }

        // // Création du nouveau monde pour ce joueur
        // $newWorld = new World();

        // $newWorld
        //     ->setUser($user)
        //     ->setWorldData($nextWorldData)
        //     ->setIsUnlocked(true);

        // // Ajout du monde à la collection du joueur
        // $user->addWorld($newWorld);

        // // Le nouveau monde devient le monde courant
        // $user->setCurrentWorld($newWorld);

        // // Paiement du monde
        // $user->setMoney(
        //     $user->getMoney() - $price
        // );

        // // Sauvegarde
        // $this->entityManager->persist($newWorld);
        // $this->entityManager->persist($user);

        // $this->entityManager->flush();

        // return $newWorld;
    }

    
}