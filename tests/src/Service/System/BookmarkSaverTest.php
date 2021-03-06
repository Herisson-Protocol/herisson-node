<?php


namespace Herisson\Service\System;

use Herisson\Entity\Bookmark;
use PHPUnit\Framework\TestCase;

class BookmarkSaverTest extends TestCase
{
    /**
     * @var SaverInterface
     */
    public $saver;

    public function setUp() : void
    {
        $this->saver = new SaverMock();
    }

    public function testDummy()
    {
        $this->assertTrue(true);
    }

    public function testSaveBookmarkAndHasContent()
    {
        $expectedContent = "Hello World";
        $bookmark = new Bookmark();
        $bookmark->setUrl("http://www.example.org");
        $bookmark->setContent($expectedContent);

        $bookmarkSaver = new BookmarkSaver($this->saver);
        $bookmarkSaver->saveBookmark($bookmark);
        $this->assertTrue($bookmarkSaver->bookmarkHasContent($bookmark));
    }

    public function testSaveBookmarkAndGetSize()
    {
        $expectedContent = "Hello World";
        $bookmark = new Bookmark();
        $bookmark->setUrl("http://www.example.org");
        $bookmark->setContent($expectedContent);
        $bookmarkSaver = new BookmarkSaver($this->saver);
        $bookmarkSaver->saveBookmark($bookmark);
        $this->assertEquals(strlen($expectedContent), $bookmarkSaver->getBookmarkSize($bookmark));
    }


}