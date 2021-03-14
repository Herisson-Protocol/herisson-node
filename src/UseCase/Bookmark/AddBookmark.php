<?php


namespace Herisson\UseCase\Bookmark;

use Herisson\Entity\Bookmark;
use Herisson\Repository\BookmarkRepository;
use Herisson\Repository\BookmarkRepositoryInterface;

class AddBookmark
{
    public $repository;

    public function __construct(BookmarkRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(AddBookmarkRequest $request, AddBookmarkResponse $response)
    {
        $bookmark = new Bookmark();
        $bookmark->setUrl($request->url);
        $this->repository->save($bookmark);
        $response->bookmark = $bookmark;

    }
}