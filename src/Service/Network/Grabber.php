<?php

namespace Herisson\Service\Network;

use Herisson\Service\Network\Exception as NetworkException;
use Herisson\Service\Message;


class Grabber
{

    public $messageService;

    public function __construct(Message $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * Get a curl object
     *
     * @param string $url  the URL to download
     * @param array|null $post the data to send via POST method
     *
     * @throws NetworkException an Exception in case php-curl is missing
     *
     * @return resource the curl object
     */
    public function getCurl(string $url, array $post = null)
    {
        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
            if (sizeof($post)) {
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
            }
            return $curl;
        } else {
            $this->messageService->addError('php-curl library is missing.');
            throw new NetworkException('php-curl library is missing.');
        }
    }

    /**
     * Download an URL
     *
     * @param string $url  the URL to download
     * @param array $post the data to send via POST method
     *
     * @throws Exception
     * @return Response the text content
     */
    public function download(string $url, $post = []) : Response
    {
        $curl = $this->getCurl($url, $post);
        
        $content =  curl_exec($curl);
        dump($content);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        $response = new Response($code, $contentType, $content);
        if ($response->isError()) {
            throw new NetworkException(sprintf("The site %s returned a %s error (%s) : %s",
                $url, $response->getCode(), $response->getMessage(), $response->getContent()),
                $response->getCode());
        }
        curl_close($curl);
        return $response;
    }

    /**
     * Check an URL
     *
     * @param string $url the URL to download
     *
     * @throws Exception
     * @return Response the HTTP status
     */
    public function check(string $url) : Response
    {
        $curl = $this->getCurl($url);
        $content = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        return new Response($code, $contentType, $content);
    }


    /**
     * Send a header reply with a specific HTTP Code
     *
     * @param integer $code the HTTP code to send to the client
     * @param bool $exit whether it should exit after sending HTTP response
     *
     * @return void
     */
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


}

