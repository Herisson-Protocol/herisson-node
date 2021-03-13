<?php


namespace Herisson\Service\System;


use Herisson\Entity\Bookmark;
use PHPUnit\Framework\TestCase;

class BookmarkSaverTest extends TestCase
{
    public $saver;

    public function setUp() : void
    {
        $this->saver = new BookmarkSaverMock();
    }

    public function testSaveBookmark()
    {
        $expectedContent = "Hello World";
        $bookmark = new Bookmark();
        $bookmark->setUrl("http://www.example.org");
        $bookmark->setContent($expectedContent);
        $this->saver->save($bookmark);
        $content = $this->saver->read($bookmark);
        $this->assertEquals($expectedContent, $content);
    }

    public function testGetDataSizeBookmark()
    {
        $expectedContent = "Hello World";
        $bookmark = new Bookmark();
        $bookmark->setUrl("http://www.example.org");
        $bookmark->setContent($expectedContent);
        $this->saver->save($bookmark);
        $contentSize = $this->saver->getDataSize($bookmark);
        $this->assertEquals(strlen($expectedContent), $contentSize);
    }



}