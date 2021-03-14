<?php


namespace Herisson\Repository;


use Doctrine\Persistence\ObjectRepository;
use Herisson\Entity\HerissonEntityInterface;

interface HerissonRepositoryInterface extends ObjectRepository
{

    public function save(HerissonEntityInterface $entity);

}