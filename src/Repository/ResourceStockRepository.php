<?php

namespace App\Repository;

use App\Entity\ResourceStock;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ResourceStock>
 *
 * @method ResourceStock|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResourceStock|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResourceStock[]    findAll()
 * @method ResourceStock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResourceStockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResourceStock::class);
    }

    /**
    * @return ResourceStock Retourne un resourcestock en fonction de l'utilisateur et du nom
    */
   public function findByUserAndName(User $user, string $name): array
   {
       return $this->createQueryBuilder('r')
           ->andWhere('r.user = :user')
           ->andWhere('r.name = :name')
           ->setParameter('user', $user)
           ->setParameter('name', $name)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }

//    /**
//     * @return ResourceStock[] Returns an array of ResourceStock objects
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

//    public function findOneBySomeField($value): ?ResourceStock
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
