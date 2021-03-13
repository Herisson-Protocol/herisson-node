<?php


namespace Herisson\Service\Network;


use GuzzleHttp\Psr7\Response;
use Herisson\Entity\Bookmark;
use Herisson\Service\Message;
use PHPUnit\Framework\TestCase;

class BookmarkGrabberTest extends TestCase
{
    public $fakeContent = "Dummy fake page content";
    public $fakeUrl = "http://www.example.org";
    public $fakeFaviconUrl = "http://www.example.org/favicon.ico";
    /**
     * @var Bookmark
     */
    public $bookmark;
    /**
     * @var GrabberGuzzleMock
     */
    public $grabber;


    public function setUp() : void
    {
        $this->bookmark =  new Bookmark();
        $this->bookmark->setUrl($this->fakeUrl);
        $this->grabber = new GrabberGuzzleMock(new Message());
    }

    public function testDummy()
    {
        $this->assertTrue(true);
    }

    public function testLoadBookmarkContent()
    {
        // Given
        $responses = [
            new Response(200, ['Content-Type' => 'text/html'], $this->fakeContent)
        ];
        $this->grabber->setResponses($responses);
        $bookmarkGrabber = new BookmarkGrabber($this->grabber);
        // When
        $bookmarkGrabber->loadContent($this->bookmark);
        // Then
        $this->assertEquals($this->fakeContent, $this->bookmark->getContent());
    }


    public function dataProviderUrlIsActive() : array
    {
        return [
            [200, true],
            [203, true],
            [302, true],
            [404, false],
            [403, false],
            [500, false],
        ];

    }

    /**
     * @param int $statusCode
     * @param bool $isActive
     * @dataProvider dataProviderUrlIsActive
     */
    public function testCheckBookmarkUrlIsActive(int $statusCode, bool $isActive)
    {
        // Given
        $responses = [
            new Response($statusCode, ['Content-Type' => 'text/html'], "Dummy"),
        ];
        $this->grabber->setResponses($responses);
        $bookmarkGrabber = new BookmarkGrabber($this->grabber);

        // When
        $bookmarkGrabber->checkBookmarkIsActive($this->bookmark);
        // Then
        $this->assertEquals($isActive, $this->bookmark->getIsActive());
    }

}