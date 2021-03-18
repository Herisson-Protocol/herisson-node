<?php


namespace Herisson\Repository;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Herisson\Entity\HerissonEntityInterface;

class HerissonRepository extends ServiceEntityRepository implements HerissonRepositoryInterface
{
    public function __construct(ManagerRegistry $registry, string $entityClass = "")
    {
        parent::__construct($registry, $entityClass);
    }

    public function save(HerissonEntityInterface $entity)
    {
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();
    }
}