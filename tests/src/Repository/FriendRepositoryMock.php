<?php


namespace Herisson\Repository;


use Herisson\Entity\Friend;
use Herisson\Entity\HerissonEntityInterface;

class FriendRepositoryMock extends HerissonRepositoryMock implements FriendRepositoryInterface
{
    protected $fields = ['id', 'name', 'url'];
    protected $objects = [
        1 => ['id' => 1, 'name' => 'sitename', 'url' => 'http://www.sitename.com'],
        2 => ['id' => 2, 'name' => 'herisson', 'url' => 'http://www.herisson.io'],
        3 => ['id' => 3, 'name' => 'example', 'url' => 'http://www.example.org'],
    ];


    protected function createFromData($objectData) : HerissonEntityInterface
    {
        $friend = new Friend();
        $friend->setId($objectData['id']);
        $friend->setName($objectData['name']);
        $friend->setUrl($objectData['url']);
        return $friend;
    }



}