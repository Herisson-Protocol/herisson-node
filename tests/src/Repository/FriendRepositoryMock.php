<?php


namespace Herisson\Repository;


use Herisson\Entity\Friend;

class FriendRepositoryMock implements FriendRepositoryInterface
{
    public $friendDatas = [
        1 => ['id' => 1, 'name' => 'sitename', 'url' => 'http://www.sitename.com'],
        2 => ['id' => 2, 'name' => 'herisson', 'url' => 'http://www.herisson.io'],
        3 => ['id' => 3, 'name' => 'example', 'url' => 'http://www.example.org'],
    ];


    private function createFriendFromData($friendData) : Friend
    {
        $friend = new Friend();
        $friend->setId($friendData['id']);
        $friend->setName($friendData['name']);
        $friend->setUrl($friendData['url']);
        return $friend;
    }
    public function find($id)
    {
        $friendData = $this->friendDatas[$id];
        return $this->createFriendFromData($friendData);
    }

    public function findAll()
    {
        $friends = [];
        foreach ($this->friendDatas as $friendData) {
            $friends[] = $this->createFriendFromData($friendData);
        }
        return $friends;
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