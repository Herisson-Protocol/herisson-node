<?php


namespace Herisson\Repository;


use Herisson\Entity\Friend;
use Herisson\Entity\HerissonEntityInterface;

class FriendRepositoryMock extends HerissonRepositoryMock implements FriendRepositoryInterface
{
    protected $fields = ['id', 'name', 'url', 'alias', 'email', 'publicKey'];
    public $objects = [
        1 => ['id' => 1, 'name' => 'sitename', 'url' => 'http://www.sitename.com', 'alias' => 'site alias', 'email' => 'email@1.org', 'publicKey' => 'pkey1'],
        2 => ['id' => 2, 'name' => 'herisson', 'url' => 'http://www.herisson.io', 'alias' => 'site alias', 'email' => 'email@2.org', 'publicKey' => 'pkey2'],
        3 => ['id' => 3, 'name' => 'example', 'url' => 'http://www.example.org', 'alias' => 'site alias', 'email' => 'email@3.org', 'publicKey' => 'pkey3'],
    ];


    protected function createFromData($objectData) : HerissonEntityInterface
    {
        $friend = new Friend();
        $friend->setId($objectData['id'] ?? 0);
        $friend->setName($objectData['name'] ?? '');
        $friend->setAlias($objectData['alias'] ?? '');
        $friend->setUrl($objectData['url'] ?? '');
        $friend->setEmail($objectData['email'] ?? '');
        $friend->setPublicKey($objectData['publicKey'] ?? '');
        return $friend;
    }



}