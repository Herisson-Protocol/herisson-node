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
        $response = $this->grabber->getResponse($bookmark->getUrl());
        $bookmark->setContent($response->getContent());
        $bookmark->setContentType($response->getType());
    }

    /**
     * Parse page title from HTML content
     *
     * This method does nothing in the following cases:
     * - the title already exists
     * - it's a binary bookmark
     *
     * @param boolean $verbose flag to set mode verbose (default true)
     *
     * @return true if title was newly found, false otherwise
     */
    public function loadTitleFromContentIfNecessary(Bookmark $bookmark) : bool
    {
        if (!$bookmark->getContent()) {
            $this->loadContent($bookmark);
        }
        $content = $bookmark->getContent();

        if (!$content || $bookmark->getTitle() || $bookmark->getIsBinary()) {
            return false;
        }
        if (preg_match("#<title>([^<]*)</title>#", $content, $match)) {
            $bookmark->setTitle($match[1]);
            /*
            if ($verbose) {
                Message::i()->addSucces(sprintf("Setting title : %s", $this->title));
            }
            */
            return true;
        }
        return false;

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