<?php

namespace App\Repository;

use App\Entity\Resource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Resource>
 *
 * @method Resource|null find($id, $lockMode = null, $lockVersion = null)
 * @method Resource|null findOneBy(array $criteria, array $orderBy = null)
 * @method Resource[]    findAll()
 * @method Resource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Resource::class);
    }

    /**
     * retourne les ressources non périmées entre la position actuelle et +/- 0.01 en longitude et latitude, 0.01 correspondant à peu près à 1km
    * @return Resource[] Returns an array of Resource objects
    */
   public function findResourcesAroundPlayer(array $position): array
   {
        // avant 0.01 pour 1km env
        $longitude = 0.0025;
        $latitude = 0.0018;
        return $this->createQueryBuilder('r')
           ->andWhere('r.isCollectable = true')
           ->andWhere('r.isCollected = false')
           ->andWhere('r.longitude BETWEEN :minLongitude AND :maxLongitude')
           ->andWhere('r.latitude BETWEEN :minLatitude AND :maxLatitude')
           ->setParameter('minLongitude', $position["longitude"] - $longitude)
           ->setParameter('maxLongitude', $position["longitude"] + $longitude)
           ->setParameter('minLatitude', $position["latitude"] - $latitude)
           ->setParameter('maxLatitude', $position["latitude"] + $latitude)
        //    ->orderBy('r.id', 'ASC')
        //    ->setMaxResults(10)
           ->getQuery()
           ->getResult()
       ;
   }
    /**
     * retourne les ressources périmées et ressources récoltées 
     * dont date péremption + temps de repop <= temps actuel cette ligne on la fait dans el controller, enfin le service
    * @return Resource[] Returns an array of Resource objects
    */
   public function findResourcesNotCollectableOrCollected(): array
   {
        // $now = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        return $this->createQueryBuilder('r')
           ->andWhere('r.isCollectable = false')
           ->orWhere('r.isCollected = true')
        //    ->andWhere('r.peremption <= :now')
        //    ->andWhere('r.latitude BETWEEN :minLatitude AND :maxLatitude')
        //    ->setParameter('now', $now->add(r.finalRepopTime))
        //    ->setParameter('maxLongitude', $position["longitude"] + $longitude)
        //    ->setParameter('minLatitude', $position["latitude"] - $latitude)
        //    ->setParameter('maxLatitude', $position["latitude"] + $latitude)
        //    ->orderBy('r.id', 'ASC')
        //    ->setMaxResults(10)
           ->getQuery()
           ->getResult()
       ;
   }

//    /**
//     * @return Resource[] Returns an array of Resource objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Resource
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
