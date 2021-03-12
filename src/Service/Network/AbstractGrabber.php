<?php

namespace Herisson\Service\Network;

use Herisson\Service\Network\NetworkException as NetworkException;
use Herisson\Service\Message;


abstract class AbstractGrabber implements GrabberInterface
{

    public $messageService;

    public function __construct(Message $messageService)
    {
        $this->messageService = $messageService;
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
    public function getContent(string $url, $post = []) : string
    {
        return (string) $this->getResponse($url, $post);
    }


    /**
     * Send a header reply with a specific HTTP Code
     *
     * @param integer $code the HTTP code to send to the client
     * @param bool $exit whether it should exit after sending HTTP response
     *
     * @return void
     */
    /*
    public static function reply(int $code, bool $exit = false)
    {
        $codes = Response::$codes;
        if (array_key_exists($code, $codes)) {
            if (!headers_sent()) {
                header("HTTP/1.1 $code ".$codes[$code]);
                if ($exit) {
                    exit;
                }
            } else {
                echo "Headers already sent.\n";
                echo $codes[$code];
            }
        } else {
            echo "Error, HTTP code $code does not exist.";
        }
    }
    */

    public function analyzeResponse($response)
    {
        if ($response->isError()) {
            throw new NetworkException(sprintf("The site %s returned a %s error (%s) : %s",
                $response->getUrl(), $response->getCode(), $response->getMessage(), $response->getContent()),
                $response->getCode());
        }
    }


}

