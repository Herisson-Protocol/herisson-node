<?php


namespace Herisson\UseCase\Bookmark;


use GuzzleHttp\Psr7\Response;
use Herisson\Entity\Bookmark;
use Herisson\Service\Message;
use Herisson\Service\Network\GrabberGuzzleMock;
use Herisson\Service\Network\GrabberInterface;
use Herisson\Service\System\SaverInterface;
use Herisson\Service\System\SaverMock;
use PHPUnit\Framework\TestCase;

class LoadAllBookmarkDataTest extends TestCase
{


    public $fakeContent = "<html><head><title>Vous Etes Perdu ?</title></head><body><h1>Perdu sur l'Internet ?</h1><h2>Pas de panique, on va vous aider</h2><strong><pre>    * <----- vous &ecirc;tes ici</pre></strong></body></html>";
    public $fakeUrl = "http://www.example.org";
    public $fakeFaviconUrl = "http://www.example.org/favicon.ico";
    public $fakeTitle = "Vous Etes Perdu ?";
    public $fakeContentType = "fake/html";

    /**
     * @var GrabberInterface
     */
    public $grabber;

    /**
     * @var SaverInterface
     */
    public $saver;

    public function setUp() : void
    {
        $this->grabber = new GrabberGuzzleMock(new Message());
        $this->saver = new SaverMock();


    }

    public function testExecute()
    {
        // Given
        $responses = [
            new Response(200, ['Content-Type' => $this->fakeContentType], $this->fakeContent)
        ];
        $this->grabber->setResponses($responses);
        $bookmark = Bookmark::createFromUrl($this->fakeUrl);

        // When
        $request = new LoadAllBookmarkDataRequest($bookmark);
        $response = new LoadAllBookmarkDataResponse();
        $usecase = new LoadAllBookmarkData($this->grabber, $this->saver);
        $usecase->execute($request, $response);
        // Then URL didnt change
        $this->assertEquals($this->fakeUrl, $bookmark->getUrl());
        // Then bookmark content has been loaded
        $this->assertEquals($this->fakeContent, $bookmark->getContent());
        $this->assertEquals($this->fakeContentType, $bookmark->getContentType());
        $this->assertEquals($this->fakeTitle, $bookmark->getTitle());
        $this->assertEquals($this->fakeFaviconUrl, $bookmark->getFaviconUrl());
        $this->assertEquals(strlen($this->fakeContent), $this->saver->getDataSize($bookmark));
        $this->assertTrue(true);
    }

}