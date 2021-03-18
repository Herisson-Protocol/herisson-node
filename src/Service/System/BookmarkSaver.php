<?php


namespace Herisson\Service\System;


use Herisson\Entity\Bookmark;

class BookmarkSaver
{

    /**
     * @var SaverInterface
     */
    public $saver;

    public function __construct(SaverInterface $saver)
    {
        $this->saver = $saver;
    }

    public function saveBookmark(Bookmark $bookmark)
    {
        $this->saver->save($bookmark);
    }

    public function bookmarkHasContent(Bookmark $bookmark)
    {
        $content = $this->getBookmarkSize($bookmark);
        return strlen($content) > 0;
    }


    public function getBookmarkSize(Bookmark $bookmark) : int
    {
        return $this->saver->getDataSize($bookmark);
    }



}