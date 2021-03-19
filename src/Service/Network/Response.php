<?php

namespace Herisson\Service\Network;

class Response extends \Symfony\Component\HttpFoundation\Response
{

    public $error = false;
    public $url;

    const ERROR_STATUS_LIMIT = 400;
    const UNKNOWN_STATUS_MESSAGE = "HTTP code not found";

    /**
     * Response constructor.
     * @param string|null $content
     * @param int $status
     * @param array $headers
     */
    public function __construct(?string $content = '', int $status = 200, array $headers = [])
    {
        parent::__construct($content, $status, $headers);
        $this->calculateErrorStatus();
    }


    /**
     * @return string
     */
    /*
    public function getUrl(): string
    {
        return $this->headers->get('X-Url') ?? '';
    }
    */

    /**
     * @param string $url
     * @return Response
     */
    /*
    public function setUrl(string $url): Response
    {
        $this->url = $url;
        return $this;
    }
    */


    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->headers->get('Content-Type') ?? '';
    }


    public function calculateErrorStatus(): object
    {
        if ($this->getStatusCode() >= static::ERROR_STATUS_LIMIT) {
            $this->setError(true);
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isError() : bool
    {
        return $this->error;
    }


    /**
     * @param bool $error
     * @return Response
     */
    public function setError(bool $error) : self
    {
        $this->error = $error;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage() : string
    {
        if (array_key_exists($this->getStatusCode(), Response::$statusTexts)) {
            return Response::$statusTexts[$this->getStatusCode()];
        }
        return static::UNKNOWN_STATUS_MESSAGE;
    }

    /**
     * @return int
     */
    public function getLength() : int
    {
        return strlen($this->getContent());
    }

}