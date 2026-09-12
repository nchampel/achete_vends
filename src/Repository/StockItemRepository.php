<?php

namespace App\Repository;

use App\Entity\StockItem;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StockItem>
 *
 * @method StockItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockItem[]    findAll()
 * @method StockItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockItem::class);
    }

    public function findStockItemsOfUserNotSold(User $user){
        // expliquer à ia que je veux ttes infos tables du joueur
        return $this->createQueryBuilder('si')
            ->innerJoin('si.item', 'i')
            ->addSelect('i')
            ->andWhere('si.user = :user')
            ->setParameter('user', $user)
            ->andWhere('si.isSold = false') 
            ->orderBy('si.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function findStockItemsOfUserSold(User $user){
        // expliquer à ia que je veux ttes infos tables du joueur
        return $this->createQueryBuilder('si')
            ->innerJoin('si.item', 'i')
            ->addSelect('i')
            ->andWhere('si.user = :user')
            ->setParameter('user', $user)
            ->andWhere('si.isSold = true') 
            ->orderBy('si.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function findStockItemsOfUserNullBuyable(){
        // expliquer à ia que je veux ttes infos tables du joueur null
        return $this->createQueryBuilder('si')
            ->innerJoin('si.item', 'i')
            ->addSelect('i')
            ->andWhere('si.user is null')
            ->andWhere('si.isBuyable = true')
            ->orderBy('si.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
    // public function findStockItemsOfUserNullBuyableApi(){
    //     // expliquer à ia que je veux ttes infos tables du joueur null
    //     return $this->createQueryBuilder('si')
    //         ->innerJoin('si.item', 'i')
    //         ->addSelect('i')
    //         ->andWhere('si.user is null')
    //         ->andWhere('si.isBuyable = true')
    //         ->orderBy('si.id', 'ASC')
    //         ->getQuery()
    //         ->getResult();
    // }
    public function findStockItemsOfStockAndUser(User $user){
        // expliquer à ia que je veux ttes infos tables du joueur et user null
        return $this->createQueryBuilder('si')
            ->innerJoin('si.item', 'i')
            ->addSelect('i')
            ->andWhere('si.user = :user')
            ->setParameter('user', $user)
            ->orWhere('si.user is null')
            ->orderBy('si.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return StockItem[] Returns an array of StockItem objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?StockItem
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
