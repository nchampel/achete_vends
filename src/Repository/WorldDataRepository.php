<?php

namespace App\Repository;

use App\Entity\WorldData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorldData>
 *
 * @method WorldData|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorldData|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorldData[]    findAll()
 * @method WorldData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorldDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorldData::class);
    }

//    /**
//     * @return WorldData[] Returns an array of WorldData objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?WorldData
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
