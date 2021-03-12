<?php


namespace Herisson\Service\Network;


use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\ResponseInterface;

class GrabberGuzzleMock extends AbstractGrabber implements GrabberInterface
{

    public $responses = [];


    public function setResponses(array $responses)
    {
        $this->responses = $responses;
    }

    /**
     * Download an URL
     *
     * @param string $url  the URL to download
     * @param array $post the data to send via POST method
     *
     * @return Response the text content
     *@throws NetworkException
     */
    public function getResponse(string $url, $post = []) : Response
    {


        // Create a mock and queue two responses.
        $mock = new MockHandler($this->responses);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        if (count($post)) {
            $res = $client->request('POST', $url, [
                'form_params' => $post,
                'http_errors' => false
            ]);
        } else {
            $res = $client->request('GET', $url, [
                'http_errors' => false
            ]);
        }
        return $this->createResponseFromGuzzle($url, $res);
    }

    public function createResponseFromGuzzle(string $url, ResponseInterface $guzzleResponse)
    {
        $code = $guzzleResponse->getStatusCode();
        $contentType = $guzzleResponse->getHeader('content-type')[0];
        $content = (string) $guzzleResponse->getBody();

        return new Response($url, $code, $contentType, $content);
    }

    /**
     * Check an URL
     *
     * @param string $url the URL to download
     *
     * @return Response the HTTP status
     *@throws NetworkException
     */
    public function check(string $url) : Response
    {
        $client = new Client();
        $res = $client->request('HEAD', $url);

        $response = $this->createResponseFromGuzzle($url, $res);

        $this->analyzeResponse($response);

        return $response;
    }



}