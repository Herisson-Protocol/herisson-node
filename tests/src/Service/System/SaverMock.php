<?php


namespace Herisson\Service\System;


use Herisson\Entity\Bookmark;

class SaverMock implements SaverInterface
{

    public $bookmarkData = [];

    public function save(Bookmark $bookmark): bool
    {
        $this->bookmarkData[$bookmark->getHash()] = $bookmark->getContent();
        return true;
    }

    public function read(Bookmark $bookmark): string
    {
        if (array_key_exists($bookmark->getHash(), $this->bookmarkData)) {
            return $this->bookmarkData[$bookmark->getHash()];
        }
        return "";
    }

    public function getDataSize(Bookmark $bookmark): int
    {
        return strlen($this->read($bookmark));
    }
}