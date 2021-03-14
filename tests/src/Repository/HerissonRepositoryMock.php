<?php


namespace Herisson\Repository;

use Exception;
use Herisson\Entity\HerissonEntityInterface;

abstract class HerissonRepositoryMock
{
    protected $objects;
    protected $fields = [];

    protected function createFromData($objectData) : HerissonEntityInterface
    {
        throw new Exception("This method should be overridden");
    }

    public function find($id)
    {
        $objectData = $this->objects[$id];
        return $this->createFromData($objectData);
    }

    public function findAll()
    {
        $objects = [];
        foreach ($this->objects as $objectData) {
            $objects[] = $this->createFromData($objectData);
        }
        return $objects;
    }

    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
    {

    }

    public function findOneBy(array $criteria)
    {

    }

    public function getClassName()
    {

    }

    public function save(HerissonEntityInterface $object)
    {
        $objectData = [];
        foreach ($this->fields as $field) {
            $objectData[$field] = call_user_func([$object, "get".ucfirst($field)]);
        }
        $this->objects[] = $objectData;
    }
}