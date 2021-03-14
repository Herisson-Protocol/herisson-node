<?php


namespace Herisson\Repository;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Herisson\Entity\HerissonEntityInterface;

class HerissonRepository extends ServiceEntityRepository implements HerissonRepositoryInterface
{

    public function save(HerissonEntityInterface $entity)
    {
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();
    }
}