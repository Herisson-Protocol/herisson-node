<?php


namespace Herisson\Service\Encryption;


class CipheredData
{
    public $data;
    public $hash;
    public $iv;

    public function __construct(string $data = "", string $hash = "", string $iv = "")
    {
        $this->data = $data;
        $this->hash = $hash;
        $this->iv = $iv;
    }

    public function getTransportableArray() : array
    {
        return [
            'data'  => base64_encode($this->data),
            'hash'  => base64_encode($this->hash),
            'iv'    => base64_encode($this->iv),
        ];
    }

    /*
    public function __toString()
    {
        return json_encode($this->getTransportableArray());
    }
    */

}