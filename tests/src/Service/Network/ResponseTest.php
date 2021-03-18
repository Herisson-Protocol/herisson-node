<?php


namespace Herisson\Service\Network;


use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{

    public function testGetType()
    {
        $response = new Response("Hello World", 200, ['Content-Type' => 'text/html']);
        $this->assertEquals('text/html', $response->getType());
    }
    public function testGetTypeFromEmptyHeaders()
    {
        $response = new Response("Hello World", 200);
        $this->assertEquals('', $response->getType());
    }
}