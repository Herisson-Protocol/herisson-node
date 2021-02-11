<?php

namespace Herisson\Repository;

use Herisson\Entity\LocalBackup;
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
    /**
     * Get the Localbackup match the id
     *
     * @param integer $backupId the id of the backup
     *
     * @return the matching Localbackup or new one
     */
    public static function get($backupId)
    {
        if (!is_numeric($backupId)) {
            return new Localbackup();
        }
        return self::getOneWhere("id=?", array($backupId));
    }

    /**
     * Get a Localbackups lit with where condition
     *
     * @param string $where the sql condition
     * @param array  $data  the value parameters
     *
     * @return an array of matching Localbackup
     */
    public static function getWhere($where, $data=array())
    {
        $pagination = Pagination::i()->getVars();
        $backups = Doctrine_Query::create()
            ->from('Herisson\Entity\Localbackup')
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
     * @return the corresponding instance of Localbackup or a new one
     */
    public static function getOneWhere($where, $data=array())
    {
        $backups = Doctrine_Query::create()
            ->from('Herisson\Entity\Localbackup')
            ->where($where)
            ->limit(1)
            ->execute($data);
        foreach ($backups as $backup) {
            return $backup;
        }
        return new Localbackup();
    }
}
