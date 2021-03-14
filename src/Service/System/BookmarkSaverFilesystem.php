<?php


namespace Herisson\Service\System;


use Herisson\Entity\Bookmark;

class BookmarkSaverFilesystem implements BookmarkSaverInterface
{

    public function save(Bookmark $bookmark): bool
    {
    }

    public function getDataSize(Bookmark $bookmark): int
    {
    }
}