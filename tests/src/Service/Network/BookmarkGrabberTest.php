<?php


namespace Herisson\Service\Network;


use GuzzleHttp\Psr7\Response;
use Herisson\Entity\Bookmark;
use Herisson\Service\Message;
use PHPUnit\Framework\TestCase;

class BookmarkGrabberTest extends TestCase
{
    public $fakeContent = "<html><head><title>Vous Etes Perdu ?</title></head><body><h1>Perdu sur l'Internet ?</h1><h2>Pas de panique, on va vous aider</h2><strong><pre>    * <----- vous &ecirc;tes ici</pre></strong></body></html>";
    public $fakeUrl = "http://www.example.org";
    public $fakeFaviconUrl = "http://www.example.org/favicon.ico";
    public $fakeTitle = "Vous Etes Perdu ?";
    public $fakeContentType = "fake/html";


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
            new Response(200, ['Content-Type' => $this->fakeContentType], $this->fakeContent)
        ];
        $this->grabber->setResponses($responses);
        $bookmarkGrabber = new BookmarkGrabber($this->grabber);
        // When
        $bookmarkGrabber->loadContent($this->bookmark);
        // Then
        $this->assertEquals($this->fakeContent, $this->bookmark->getContent());
        $this->assertEquals($this->fakeContentType, $this->bookmark->getContentType());
    }


    public function testLoadBookmarkTitleFromContent()
    {
        // Given
        $responses = [
            new Response(200, ['Content-Type' => 'text/html'], $this->fakeContent)
        ];
        $this->grabber->setResponses($responses);
        $bookmarkGrabber = new BookmarkGrabber($this->grabber);
        // When
        $bookmarkGrabber->loadContent($this->bookmark);
        $bookmarkGrabber->loadTitleFromContentIfNecessary($this->bookmark);
        // Then
        $this->assertEquals($this->fakeTitle, $this->bookmark->getTitle());
    }


    public function testLoadBookmarkTitleFromContentFromScratch()
    {
        // Given
        $responses = [
            new Response(200, ['Content-Type' => 'text/html'], $this->fakeContent)
        ];
        $this->grabber->setResponses($responses);
        $bookmarkGrabber = new BookmarkGrabber($this->grabber);
        // When
        $bookmarkGrabber->loadTitleFromContentIfNecessary($this->bookmark);
        // Then
        $this->assertEquals($this->fakeTitle, $this->bookmark->getTitle());
    }

    public function testDontLoadBookmarkTitleFromContentIfTitleExists()
    {
        // Given
        $responses = [
            new Response(200, ['Content-Type' => 'text/html'], $this->fakeContent)
        ];
        $this->grabber->setResponses($responses);
        $bookmarkGrabber = new BookmarkGrabber($this->grabber);
        $existingTitle = "Already existing title";
        $this->bookmark->setTitle($existingTitle);
        // When
        $bookmarkGrabber->loadTitleFromContentIfNecessary($this->bookmark);
        // Then
        $this->assertEquals($existingTitle, $this->bookmark->getTitle());
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