<?php

namespace Herisson\Repository;

use Herisson\Entity\RemoteBackup;
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
    /**
     * Get the Backup match the id
     *
     * @param integer $backupId the id of the backup
     *
     * @return the matching Backup or new one
     */
    public static function get($backupId)
    {
        if (!is_numeric($backupId)) {
            return new Backup();
        }
        return self::getOneWhere("id=?", array($backupId));
    }

    /**
     * Get a Backups lit with where condition
     *
     * @param string $where the sql condition
     * @param array  $data  the value parameters
     *
     * @return an array of matching Backup
     */
    public static function getWhere($where, $data=array())
    {
        $pagination = Pagination::i()->getVars();
        $backups = Doctrine_Query::create()
            ->from('Herisson\Entity\Backup')
            ->where($where)
            ->limit($pagination['limit'])
            ->offset($pagination['offset'])
            ->execute($data);
        return $backups;
    }


    /**
     * Get one item with where paremeters
     *
     * @param string $where the sql condition
     * @param array  $data  the value parameters
     *
     * @return the corresponding instance of Backup or a new one
     */
    public static function getOneWhere($where, $data=array())
    {
        $backups = Doctrine_Query::create()
            ->from('Herisson\Entity\Backup')
            ->where($where)
            ->limit(1)
            ->execute($data);
        foreach ($backups as $backup) {
            return $backup;
        }
        return new Backup();
    }

}
