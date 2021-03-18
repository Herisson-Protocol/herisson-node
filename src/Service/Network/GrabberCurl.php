<?php


namespace Herisson\Service\Network;


class GrabberCurl extends AbstractGrabber implements GrabberInterface
{



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
    private function getCurl(string $url, array $post = null)
    {
        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
            //curl_setopt($curl, CURLOPT_VERBOSE, true);
            if ($post && count($post)) {
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
     * @return Response the text content
     *@throws NetworkException
     */
    public function getResponse(string $url, $post = []) : Response
    {
        $curl = $this->getCurl($url, $post);

        $response = $this->createResponseFromCurl($url, $curl);

        $this->analyzeResponse($response);

        curl_close($curl);
        return $response;
    }

    private function createResponseFromCurl($url, $curl)
    {
        $content =  curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
//        return new Response($url, $code, $contentType, $content);
        return new Response($content, $code, ['Content-Type' => $contentType, 'X-Url' => $url]);
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
        $curl = $this->getCurl($url);
        return $this->createResponseFromCurl($url, $curl);
        /*
        $content = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        return new Response($code, $contentType, $content);
        */
    }

}