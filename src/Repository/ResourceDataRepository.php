<?php

namespace App\Repository;

use App\Entity\ResourceData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ResourceData>
 *
 * @method ResourceData|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResourceData|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResourceData[]    findAll()
 * @method ResourceData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResourceDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResourceData::class);
    }

//    /**
//     * @return ResourceData[] Returns an array of ResourceData objects
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

//    public function findOneBySomeField($value): ?ResourceData
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
