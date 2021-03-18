<?php


namespace Herisson\UseCase\Bookmark;


use Herisson\Entity\Bookmark;

class LoadAllBookmarkDataRequest
{

    /**
     * @var Bookmark;
     */
    public $bookmark;

    public function __construct(Bookmark $bookmark)
    {
        $this->bookmark = $bookmark;
    }
}