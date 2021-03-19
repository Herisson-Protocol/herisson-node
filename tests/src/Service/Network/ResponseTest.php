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

    public function testGetLength()
    {
        $size = 30;
        $response = new Response(str_repeat("H", $size), 200, ['Content-Type' => 'text/html']);
        $this->assertEquals($size, $response->getLength());
    }

    public function testGetTypeFromEmptyHeaders()
    {
        $response = new Response("Hello World", 200);
        $this->assertEquals('', $response->getType());
    }


    public function messageDataProvider() : array
    {
        return [
            [200, 'OK'],
            [202, 'Accepted'],
            [403, 'Forbidden'],
            [404, 'Not Found'],
            [500, 'Internal Server Error'],
            [199, 'HTTP code not found'],
        ];
    }

    /**
     * @dataProvider messageDataProvider
     */
    public function testGetMessage($statusCode, $message)
    {
        $response = new Response("Hello World", $statusCode);
        $this->assertEquals($message, $response->getMessage());
    }
}