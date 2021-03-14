<?php


namespace Herisson\Repository;


use Herisson\Entity\HerissonEntityInterface;
use Herisson\Entity\Option;

class OptionRepositoryMock extends HerissonRepositoryMock implements OptionRepositoryInterface
{
    protected $fields = ['id', 'name', 'value'];
    protected $objects = [
        1 => ['id' => 1, 'name' => 'sitename', 'value' => 'HerissonSite'],
        2 => ['id' => 2, 'name' => 'email', 'value' => 'admin@example.org'],
        3 => ['id' => 3, 'name' => 'publicKey', 'value' => '-- BEGIN PUBLIC KEY -- '],
        4 => ['id' => 4, 'name' => 'privateKey', 'value' => '-- BEGIN PRIVATE KEY --'],
        5 => ['id' => 5, 'name' => 'siteurl', 'value' => 'http://localhost:8000'],
        6 => ['id' => 6, 'name' => 'basePath', 'value' => 'bookmarks'],
    ];

    public function createFromData($objectData) : HerissonEntityInterface
    {
        $option = new Option();
        $option->setId($objectData['id']);
        $option->setName($objectData['name']);
        $option->setValue($objectData['value']);
        return $option;
    }

}