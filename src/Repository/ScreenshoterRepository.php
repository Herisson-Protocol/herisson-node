<?php

namespace App\Repository;

use App\Entity\Screenshoter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Screenshoter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Screenshoter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Screenshoter[]    findAll()
 * @method Screenshoter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScreenshoterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Screenshoter::class);
    }

    // /**
    //  * @return Screenshoter[] Returns an array of Screenshoter objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Screenshoter
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
