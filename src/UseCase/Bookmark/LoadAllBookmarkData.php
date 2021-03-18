<?php


namespace Herisson\UseCase\Bookmark;


use Herisson\Service\System\BookmarkSaver;
use Herisson\Service\System\SaverFilesystem;
use Herisson\Service\System\SaverInterface;
use Herisson\UseCase\Bookmark\LoadAllBookmarkDataRequest;
use Herisson\Service\Network\BookmarkGrabber;
use Herisson\Service\Network\GrabberInterface;

class LoadAllBookmarkData
{

    /**
     * @var GrabberInterface
     */
    public $grabber;

    /**
     * @var SaverInterface
     */
    public $saver;

    public function __construct(GrabberInterface $grabber, SaverInterface $saver)
    {
        $this->grabber = $grabber;
        $this->saver = $saver;
    }

    public function execute(LoadAllBookmarkDataRequest $request, LoadAllBookmarkDataResponse $response)
    {
        $bookmarkGrabber = new BookmarkGrabber($this->grabber);
        $bookmarkGrabber->loadContent($request->bookmark);
        $bookmarkGrabber->loadTitleFromContentIfNecessary($request->bookmark);

        $bookmarkSaver = new BookmarkSaver($this->saver);
        $bookmarkSaver->saveBookmark($request->bookmark);

        return $response;
    }
}