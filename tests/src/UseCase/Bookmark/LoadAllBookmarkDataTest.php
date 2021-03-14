<?php


namespace Herisson\UseCase\Bookmark;


use Herisson\Entity\Bookmark;
use Herisson\Repository\BookmarkRepositoryMock;

class LoadAllBookmarkDataTest
{

    public function testExecute()
    {
        $url = "http://www.example.org";
        $bookmark = Bookmark::createFromUrl($url);
        $repo = new BookmarkRepositoryMock();
        $request = new LoadAllBookmarkDataTestRequest($bookmark);
        $response = new LoadAllBookmarkDataTestResponse();
        $usecase = new LoadAllBookmarkDataTest($repo);
        $usecase->execute($request, $response);
        $this->assertEquals($url, $response->bookmark->getUrl());
    }

}