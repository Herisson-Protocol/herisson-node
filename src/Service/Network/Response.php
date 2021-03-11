<?php

namespace Herisson\Service\Network;

class Response
{

    public $content;
    public $type;
    public $code;
    public $error = false;
    public $message;
    public $url;

    /**
     * HTTP Code
     *
     * @source http://www.checkupdown.com/status/E417.html
     */
    public static $codes = [
        "100" => "Continue",
        "101" => "Switching Protocols",
        "102" => "Processing (WebDAV; RFC 2518)",

        "200" => "OK",
        "201" => "Created",
        "202" => "Accepted",
        "203" => "Non-Authoritative Information (since HTTP/1.1)",
        "204" => "No Content",
        "205" => "Reset Content",
        "206" => "Partial Content",
        "207" => "Multi-Status (WebDAV; RFC 4918)",
        "208" => "Already Reported (WebDAV; RFC 5842)",
        "226" => "IM Used (RFC 3229)",

        "300" => "Multiple Choices",
        "301" => "Moved Permanently",
        "302" => "Found",
        "303" => "See Other (since HTTP/1.1)",
        "304" => "Not Modified",
        "305" => "Use Proxy (since HTTP/1.1)",
        "306" => "Switch Proxy",
        "307" => "Temporary Redirect (since HTTP/1.1)",
        "308" => "Permanent Redirect (experimental Internet-Draft)[10]",

        "400" => "Bad Request",
        "401" => "Unauthorized",
        "402" => "Payment Required",
        "403" => "Forbidden",
        "404" => "Not Found",
        "405" => "Method Not Allowed",
        "406" => "Not Acceptable",
        "407" => "Proxy Authentication Required",
        "408" => "Request Timeout",
        "409" => "Conflict",
        "410" => "Gone",
        "411" => "Length Required",
        "412" => "Precondition Failed",
        "413" => "Request Entity Too Large",
        "414" => "Request-URI Too Long",
        "415" => "Unsupported Media Type",
        "416" => "Requested Range Not Satisfiable",
        "417" => "Expectation Failed",
        "418" => "I'm a teapot (RFC 2324)",
        "420" => "Enhance Your Calm (Twitter)",
        "422" => "Unprocessable Entity (WebDAV; RFC 4918)",
        "423" => "Locked (WebDAV; RFC 4918)",
        "424" => "Failed Dependency (WebDAV; RFC 4918)",
        "425" => "Unordered Collection (Internet draft)",
        "426" => "Upgrade Required (RFC 2817)",
        "428" => "Precondition Required (RFC 6585)",
        "429" => "Too Many Requests (RFC 6585)",
        "431" => "Request Header Fields Too Large (RFC 6585)",
        "444" => "No Response (Nginx)",
        "449" => "Retry With (Microsoft)",
        "450" => "Blocked by Windows Parental Controls (Microsoft)",
        "451" => "Unavailable For Legal Reasons (Internet draft)",
        "499" => "Client Closed Request (Nginx)",

        "500" => "Internal Server Error",
        "501" => "Not Implemented",
        "502" => "Bad Gateway",
        "503" => "Service Unavailable",
        "504" => "Gateway Timeout",
        "505" => "HTTP Version Not Supported",
        "506" => "Variant Also Negotiates (RFC 2295)",
        "507" => "Insufficient Storage (WebDAV; RFC 4918)",
        "508" => "Loop Detected (WebDAV; RFC 5842)",
        "509" => "Bandwidth Limit Exceeded (Apache bw/limited extension)",
        "510" => "Not Extended (RFC 2774)",
        "511" => "Network Authentication Required (RFC 6585)",
        "598" => "Network read timeout error (Unknown)",
        "599" => "Network connect timeout error (Unknown)",
    ];

    public static $types = [
        "text/html",
        "image/png",
        "image/jpg",
        "image/jpeg",
        "image/gif",
    ];

    /**
     * Response constructor.
     * @param int $code
     * @param string $type
     * @param string $content
     */
    public function __construct(string $url = "", int $code = 0, string $type = "", string $content = "")
    {
        $this->url = $url;
        $this->setCode($code);
        $this->type = $type;
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return Response
     */
    public function setUrl(string $url): Response
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent() : string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return Response
     */
    public function setContent(string $content) : self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return Response
     */
    public function setType($type) : self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getCode() : int
    {
        return $this->code;
    }

    /**
     * @param int $code
     * @return Response
     */
    public function setCode(int $code) : self
    {
        $this->code = $code;
        if ($code >= 400) {
            $this->setError(true);
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isError() : bool
    {
        return $this->getError();
    }

    /**
     * @return bool
     */
    public function getError() : bool
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
        //dump($this->getCode());
        if (array_key_exists($this->getCode(), Response::$codes)) {
            return Response::$codes[$this->getCode()];
        }
        return "HTTP code not found";
    }

    /**
     * @return int
     */
    public function getLength() : int
    {
        return strlen($this->getContent());
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return $this->getContent();
    }
}