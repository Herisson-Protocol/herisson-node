<?php


namespace Herisson\Service\Network;


use Herisson\Service\Message;
use PHPUnit\Framework\TestCase;

class GrabberGuzzleTest extends TestCase
{

    public function testGetContent()
    {
        // Given
        $message = new Message();
        $grabber = new GrabberGuzzle($message);
        $url = "http://perdu.com";
        $expectedContent = "<html><head><title>Vous Etes Perdu ?</title></head><body><h1>Perdu sur l'Internet ?</h1><h2>Pas de panique, on va vous aider</h2><strong><pre>    * <----- vous &ecirc;tes ici</pre></strong></body></html>\n";
        // When
        $content = $grabber->getContent($url);
        // Then
        $this->assertEquals($expectedContent, $content);
    }

    public function testResponse()
    {
        // Given
        $message = new Message();
        $grabber = new GrabberGuzzle($message);
        $url = "http://perdu.com";
        $expectedCode = 200;
        $expectedLength = 204;
        // When
        $response = $grabber->getResponse($url);
        // Then
        $this->assertEquals($expectedCode, $response->getCode());
        $this->assertEquals($expectedLength, $response->getLength());
    }


    public function testCheck()
    {
        // Given
        $message = new Message();
        $grabber = new GrabberGuzzle($message);
        $url = "http://perdu.com";
        $expectedCode = 200;
        $expectedLength = 204;
        // When
        $response = $grabber->check($url);
        // Then
        $this->assertEquals($expectedCode, $response->getCode());
    }
}