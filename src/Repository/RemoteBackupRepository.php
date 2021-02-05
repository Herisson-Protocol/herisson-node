<?php

namespace App\Repository;

use App\Entity\RemoteBackup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RemoteBackup|null find($id, $lockMode = null, $lockVersion = null)
 * @method RemoteBackup|null findOneBy(array $criteria, array $orderBy = null)
 * @method RemoteBackup[]    findAll()
 * @method RemoteBackup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RemoteBackupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RemoteBackup::class);
    }

    // /**
    //  * @return RemoteBackup[] Returns an array of RemoteBackup objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RemoteBackup
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
