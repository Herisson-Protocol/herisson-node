<?php


namespace Herisson\Service\Network;


use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class GrabberGuzzle extends AbstractGrabber implements GrabberInterface
{

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
        $client = new Client();
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

        $response = $this->createResponseFromGuzzle($url, $res);

        $this->analyzeResponse($response);
        return $response;
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