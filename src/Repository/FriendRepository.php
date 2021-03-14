<?php

namespace Herisson\Repository;

use Herisson\Entity\Friend;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Friend|null find($id, $lockMode = null, $lockVersion = null)
 * @method Friend|null findOneBy(array $criteria, array $orderBy = null)
 * @method Friend[]    findAll()
 * @method Friend[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FriendRepository extends HerissonRepository implements FriendRepositoryInterface
{
    private $registry;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Friend::class);
        $this->registry = $registry;
    }

    public function remove($friend)
    {

        $this->registry->remove($friend);
    }

    /**
     * Get the Friend match the id
     *
     * @param integer $friendId the id of the friend
     *
     * @return the matching Friend or new one
     */
    public static function get($friendId)
    {
        if (!is_numeric($friendId)) {
            return new Friend();
        }
        return self::getOneWhere("id=?", array($friendId));
    }

    /**
     * Retrieve all friends
     *
     * @param boolean $paginate wether we should paginate this select
     * 
     * @return a list of all Friends object
     */
    public function getAll()
    {
        return $this->createQueryBuilder('f')
            ->orderBy('f.name', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;    }

    /**
     * Retrieve all actives friends
     *
     * @param boolean $paginate wether we should paginate this select
     * 
     * @return a list of all active Friends object
     */
    public function getActives()
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.is_active = :val')
            ->setParameter('val', 1)
            ->orderBy('f.name', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }



    /**
     * Get one item with where paremeters
     *
     * @param string $where the sql condition
     * @param array  $data  the value parameters
     *
     * @return the corresponding instance of Friend or a new one
     */
    public static function getOneWhere($where, $data=array())
    {
        $friends = Doctrine_Query::create()
            ->from('Herisson\Entity\Friend')
            ->where($where)
            ->limit(1)
            ->execute($data);
        foreach ($friends as $friend) {
            return $friend;
        }
        return new Friend();
    }

}
