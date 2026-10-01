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
       return $this->createQueryBuilder('r')
           ->andWhere('r.isCollectable = true')
           ->andWhere('r.isCollected = false')
           ->andWhere('r.longitude BETWEEN :minLongitude AND :maxLongitude')
           ->andWhere('r.latitude BETWEEN :minLatitude AND :maxLatitude')
           ->setParameter('minLongitude', $position["longitude"] - 0.01)
           ->setParameter('maxLongitude', $position["longitude"] + 0.01)
           ->setParameter('minLatitude', $position["latitude"] - 0.01)
           ->setParameter('maxLatitude', $position["latitude"] + 0.01)
           ->orderBy('r.id', 'ASC')
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
