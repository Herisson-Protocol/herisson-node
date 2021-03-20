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
        throw new Exception("This method has not yet been implemented");
    }

    public function findOneBy(array $criteria)
    {
        if (count($criteria)) {
            $firstCriteria = key($criteria);
            $firstValue = $criteria[$firstCriteria];
        }
        foreach ($this->objects as $objectData) {
            if (isset($objectData->{$firstCriteria})) {
                if ($objectData->{$firstCriteria} === $firstValue) {
                    return $this->createFromData($objectData);
                }
            }
        }
        return null;
    }

    public function getClassName()
    {

    }

    public function getNextId()
    {
        return max(array_keys($this->objects))+1;
    }

    public function save(HerissonEntityInterface $object) : int
    {
        $objectData = [];
        foreach ($this->fields as $field) {
            $objectData[$field] = call_user_func([$object, "get".ucfirst($field)]);
        }
        $id = $object->getId() ?: $this->getNextId();
        $objectData['id'] = $id;
        $this->objects[$id] = $objectData;
        return $id;
    }
}