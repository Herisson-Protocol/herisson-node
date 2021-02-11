<?php

namespace Herisson\Repository;

use Herisson\Entity\Screenshoter;
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
    /**
     * Get all screenshots tools
     *
     * @return array the list of screenshots tools
     */
    public static function getAll()
    {
        $screenshots = Doctrine_Query::create()
            ->from('Herisson\Entity\Screenshot')
            ->orderby("id")
            ->execute();
        return $screenshots;
    }

    /**
     * Get one screenshots tool from id
     *
     * @param integer $id the id of the screenshot tool
     *
     * @return mixed the screenshot tool object
     */
    public static function get($id)
    {
        if (!is_numeric($id)) {
            return new Screenshot();
        }
        $screenshots = Doctrine_Query::create()
            ->from('Herisson\Entity\Screenshot')
            ->where("id=?")
            ->execute(array($id));
        foreach ($screenshots as $screenshot) {
            return $screenshot;
        }
        return new Screenshot();
    }

}
