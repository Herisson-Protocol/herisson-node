<?php


namespace Herisson\UseCase\Bookmark;


use Herisson\Repository\BookmarkRepositoryMock;
use PHPUnit\Framework\TestCase;

class AddBookmarkTest extends TestCase
{


    public function testExecute()
    {
        $url = "http://www.example.org";
        $repo = new BookmarkRepositoryMock();
        $request = new AddBookmarkRequest($url);
        $response = new AddBookmarkResponse();
        $usecase = new AddBookmark($repo);
        $usecase->execute($request, $response);
        $this->assertEquals($url, $response->bookmark->getUrl());
    }

}