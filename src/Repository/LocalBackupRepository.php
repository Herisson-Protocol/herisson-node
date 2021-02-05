<?php

namespace App\Repository;

use App\Entity\LocalBackup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LocalBackup|null find($id, $lockMode = null, $lockVersion = null)
 * @method LocalBackup|null findOneBy(array $criteria, array $orderBy = null)
 * @method LocalBackup[]    findAll()
 * @method LocalBackup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocalBackupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocalBackup::class);
    }

    // /**
    //  * @return LocalBackup[] Returns an array of LocalBackup objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LocalBackup
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
