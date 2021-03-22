<?php


namespace Herisson\Repository;


use Herisson\Entity\Friend;
use Herisson\Entity\HerissonEntityInterface;

class FriendRepositoryMock extends HerissonRepositoryMock implements FriendRepositoryInterface
{
    protected $fields = ['id', 'name', 'url', 'alias', 'email', 'publicKey', 'isActive', 'isValidatedByUs', 'isValidatedByHim'];
    public $objects = [
        1 => [
            'id' => 1,
            'name' => 'sitename',
            'url' => 'http://www.sitename.com',
            'alias' => 'site alias',
            'email' => 'email@1.org',
            'publicKey' => 'pkey1',
            'is_active' => false,
            'is_validated_by_us' => false,
            'is_validated_by_him' => false,
        ],
        2 => [
            'id' => 2,
            'name' => 'herisson',
            'url' => 'http://www.herisson.io',
            'alias' => 'site alias',
            'email' => 'email@2.org',
            'publicKey' => 'pkey2',
            'is_active' => false,
            'is_validated_by_us' => false,
            'is_validated_by_him' => false,
        ],
        3 => [
            'id' => 3,
            'name' => 'example',
            'url' => 'ht
            tp://www.example.org',
            'alias' => 'site alias',
            'email' => 'email@3.org',
            'publicKey' => 'pkey3',
            'is_active' => false,
            'is_validated_by_us' => false,
            'is_validated_by_him' => false,
        ],
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
        $friend->setIsActive($objectData['isActive'] ?? false);
        $friend->setIsValidatedByUs($objectData['isValidatedByUs'] ?? false);
        $friend->setIsValidatedByHim($objectData['isValidatedByHim'] ?? false);
        return $friend;
    }



}