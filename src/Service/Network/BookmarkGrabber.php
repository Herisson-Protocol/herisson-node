<?php


namespace Herisson\Service\Network;


use Herisson\Entity\Bookmark;

class BookmarkGrabber
{

    public $grabber;

    public function __construct(GrabberInterface $grabber)
    {
        $this->grabber = $grabber;
    }
    public function loadContent(Bookmark $bookmark)
    {
        $content = $this->grabber->getContent($bookmark->getUrl());
        $bookmark->setContent($content);
    }

    public function checkBookmarkIsActive(Bookmark $bookmark)
    {
        $response = $this->grabber->check($bookmark->getUrl());
        if ($response->isError()) {
            $bookmark->setIsActive(false);
        } else {
            $bookmark->setIsActive(true);
        }
    }

}