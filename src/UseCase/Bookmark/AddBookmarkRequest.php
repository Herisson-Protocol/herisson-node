<?php


namespace Herisson\UseCase\Bookmark;


class AddBookmarkRequest
{
    public $url;

    public function __construct($url)
    {
        $this->url = $url;
    }
}