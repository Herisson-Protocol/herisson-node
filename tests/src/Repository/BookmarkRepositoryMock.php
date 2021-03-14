<?php


namespace Herisson\Repository;


use Herisson\Entity\Bookmark;
use Herisson\Entity\HerissonEntityInterface;

class BookmarkRepositoryMock extends HerissonRepositoryMock implements BookmarkRepositoryInterface
{
    protected $fields = ['id', 'url'];
    protected $objects = [
        1 => ['id' => 1, 'url' => 'http://www.sitename.com'],
        2 => ['id' => 2, 'url' => 'http://www.herisson.io'],
        3 => ['id' => 3, 'url' => 'http://www.example.org'],
    ];


    protected function createFromData($objectData) : HerissonEntityInterface
    {
        $bookmark = new Bookmark();
        $bookmark->seturl($objectData['url']);
        return $bookmark;
    }

}