<?php


namespace Herisson\Repository;


use Herisson\Entity\Option;

class OptionRepositoryMock implements OptionRepositoryInterface
{
    public $optionNames = [
        1 => ['id' => 1, 'name' => 'sitename', 'value' => 'HerissonSite'],
        2 => ['id' => 2, 'name' => 'email', 'value' => 'admin@example.org'],
        3 => ['id' => 3, 'name' => 'publicKey', 'value' => '-- BEGIN PUBLIC KEY -- '],
        4 => ['id' => 4, 'name' => 'privateKey', 'value' => '-- BEGIN PRIVATE KEY --'],
        5 => ['id' => 5, 'name' => 'siteurl', 'value' => 'http://localhost:8000'],
        6 => ['id' => 6, 'name' => 'basePath', 'value' => 'bookmarks'],
    ];

    /**
     * OptionRepositoryMock constructor.
     */
    public function __construct()
    {
    }

    public function createOptionFromData($optionData) : Option
    {
        $option = new Option();
        $option->setId($optionData['id']);
        $option->setName($optionData['name']);
        $option->setValue($optionData['value']);
        return $option;
    }
    public function find($id)
    {
        $optionData = $this->optionNames[$id];
        return $this->createOptionFromData($optionData);
    }

    public function findAll()
    {
        $options = [];
        foreach ($this->optionNames as $optionData) {
            $options[] = $this->createOptionFromData($optionData);
        }
        return $options;
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
}