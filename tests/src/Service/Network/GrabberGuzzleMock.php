<?php


namespace Herisson\Service\Network;


use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\ResponseInterface;

class GrabberGuzzleMock extends AbstractGrabber implements GrabberInterface
{

    public $responses = [];
    private $client;


    public function setResponses(array $responses)
    {
        $this->responses = $responses;
        // Create a mock and queue two responses.
        $mock = new MockHandler($this->responses);

        $handlerStack = HandlerStack::create($mock);
        $this->client = new Client(['handler' => $handlerStack]);
    }

    /**
     * Download an URL
     *
     * @param string $url  the URL to download
     * @param array $post the data to send via POST method
     *
     * @return Response the text content
     //* @throws NetworkException
     */
    public function getResponse(string $url, $post = []) : Response
    {
        //error_log("Calling $url");
        $this->checkIsCallableOrThrowException();

        if (count($post)) {
            $res = $this->client->request('POST', $url, [
                'form_params' => $post,
                'http_errors' => false
            ]);
        } else {
            $res = $this->client->request('GET', $url, [
                'http_errors' => false
            ]);
        }
        return $this->createResponseFromGuzzle($url, $res);
    }

    public function createResponseFromGuzzle(string $url, ResponseInterface $guzzleResponse) : Response
    {
        $code = $guzzleResponse->getStatusCode();
        $contentType = $guzzleResponse->getHeader('content-type')[0];
        $content = (string) $guzzleResponse->getBody();

        return new Response($content, $code, ['Content-Type' => $contentType, 'X-Url' => $url]);
    }

    private function checkIsCallableOrThrowException()
    {
        if (!$this->client) {
            throw new \Exception("Client not set up. Have you called setResponses ?");
        }
    }

    /**
     * Check an URL
     *
     * @param string $url the URL to download
     *
     * @return Response the HTTP status
     * @throws NetworkException
     */
    public function check(string $url) : Response
    {
        $this->checkIsCallableOrThrowException();

        $res = $this->client->request('HEAD', $url, [
            'http_errors' => false
        ]);

        return $this->createResponseFromGuzzle($url, $res);

    }



}