<?php


namespace Herisson\Service\Encryption;


class KeyPair
{
    private $private;
    private $public;

    public static function generate() : KeyPair
    {
        // Create the keypair
        $res = openssl_pkey_new();

        // Get private key
        openssl_pkey_export($res, $private);

        // Get public key
        $pubkey = openssl_pkey_get_details($res);
        $public = $pubkey["key"];

        $key = new KeyPair();
        $key->setPrivate($private);
        $key->setPublic($public);
        return $key;
    }

    public function setPrivate($private)
    {
        $this->private = $private;
    }

    public function setPublic($public)
    {
        $this->public = $public;
    }
    public function getPrivate() : string
    {
        return $this->private;
    }

    public function getPublic() : string
    {
        return $this->public;
    }

}