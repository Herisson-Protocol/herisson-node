<?php

namespace Herisson\Service\Encryption;

use Herisson\Service\Encryption\EncryptionException as EncryptionException;

class Encryptor
{

    public $publicKeyHeader = "-----BEGIN PUBLIC KEY-----\n";
    public $publicKeyFooter = "\n-----END PUBLIC KEY-----";
    public $privateKeyHeader = "-----BEGIN PRIVATE KEY-----\n";
    public $privateKeyFooter = "\n-----END PRIVATE KEY-----";

    /**
     * Encryptor method for long data
     */
    public static $method = "aes256";


    /**
     * Hash a variable in sha256
     *
     * @param string $data the data to hash
     *
     * @return string hashed data in sha256
     */
    public function hash(string $data) : string
    {
        return hash("sha256", $data);
    }

    /**
     * Create a random IV string
     *
     * @param integer $length the length of the expected string (default=16)
     *
     * @return string a random IV string of given length
     */
    public function createIV(int $length=16) : string
    {
        if (function_exists('mcrypt_create_iv')) {
            return mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
        }
        $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        return substr(str_shuffle($chars), 0, $length);
    }
    /*

    private function isValidPrivateKey($key) {
        return preg_match("/".$this->privateKeyHeader."/", $key) && preg_match("/".$this->privateKeyFooter."/", $key);
    }

    private function formatPrivateKey($key)
    {
        if ($this->isValidPrivateKey($key)) {
            return $key;
        }
        return $this->privateKeyHeader. wordwrap($key, 64, "\n", true) . $this->privateKeyFooter;
    }

    private function isValidPublicKey($key) {
        return preg_match("/".$this->publicKeyHeader."/", $key) && preg_match("/".$this->publicKeyFooter."/", $key);
    }

    private function formatPublicKey($key)
    {
        if ($this->isValidPublicKey($key)) {
            return $key;
        }
        return $this->publicKeyHeader. wordwrap($key, 64, "\n", true) . $this->publicKeyFooter;
    }
    */
    /**
     * Encrypt data using a public key
     *
     * @param mixed $data the data to encrypt
     * @param mixed $key  optional public key,
     *        if none given, the $this->public key is used
     *
     * @return string the encrypted data
     */
    function publicEncrypt($data, $key) : string
    {
        if (!openssl_public_encrypt($data, $dataCrypted, $key)) {
            throw new EncryptionException(
                'Error while encrypting with public key');
        }
        return base64_encode($dataCrypted);
    }

    /**
     * Decrypt encrypted data using a public key
     *
     * @param mixed $dataCrypted the encrypted data
     * @param mixed $key         optional public key, if none given, the $this->public key is used
     *
     * @return string the clear data
     */
    function publicDecrypt($dataCrypted, $key) : string
    {
        if (!openssl_public_decrypt(base64_decode($dataCrypted), $data, $key)) {
            throw new EncryptionException('Error while decrypting with public key');
        }
        return $data;
    }

    /**
     * Encrypt data using a private key
     *
     * @param mixed $data the data to encrypt
     * @param mixed $key  optional private key, if none given, the $this->private key is used
     *
     * @return string the encrypted data
     */
    function privateEncrypt($data, $key) : string
    {
        if (!openssl_private_encrypt($data, $dataCrypted, $key)) {
            throw new EncryptionException(
                'Error while encrypting with private key');
        }
        return base64_encode($dataCrypted);
    }

    /**
     * Decrypt encrypted data using a private key
     *
     * @param mixed $dataCrypted the encrypted data
     * @param mixed $key          optional private key, if none given, the $this->private key is used
     *
     * @return string the clear data
     */
    function privateDecrypt($dataCrypted, $key) : string
    {
        //print_r($this->formatPrivateKey($key));

        if (!openssl_private_decrypt(base64_decode($dataCrypted), $data, $key)) {
            throw new EncryptionException(
                'Error while decrypting with private key');
        }
        return $data;
    }

    /**
     * Encrypt long data using a public key
     *
     * Since we want to encrypt long data, we encrypt the hash of the data with 
     * the public key, and we encrypt the data with self::method (eg. aes256) 
     * and the password is the encrypted hash
     *
     * @param mixed $data the data to encrypt
     * @param mixed $key  optional public key, if none given, the $this->public key is used
     *
     * @return array an array with 'data' => the encrypted data, 'hash' => the encrypted hash
     */
    function publicEncryptLongData($data, $publicKey) : CipheredData
    {
        $hash = $this->hash($data);

        $iv = $this->createIV();

        if (!openssl_public_encrypt($hash, $hash_crypted, $publicKey)) {
            throw new EncryptionException(
                'Error while encrypting hash with public key');
        }

        $data_crypted = null;
        if (!($data_crypted = openssl_encrypt($data, self::$method, $hash, 0, $iv))) {
            throw new EncryptionException(
                'Error while encrypting long data with encryption method');
        }

        return new CipheredData($data_crypted, $hash_crypted, $iv);
    }


    /**
     * Decrypt long data using a public key
     *
     * Since we want to decrypt a long encrypted data, we decrypt the crypted hash with the public key,
     * and we decrypt the long data with the decrypted hash using encryption 
     * method
     *
     * @param mixed $data_crypted the crypted data (crypted with the hash)
     * @param mixed $hash_crypted the crypted hash (crypted with the private key)
     * @param mixed $iv           the initialization vector to increase cipher security
     * @param mixed $key          optional public key, if none given, the $this->public key is used
     *
     * @return string the decrypted data
     */
    public function publicDecryptLongData(CipheredData $cipheredData, $publicKey)
    {


        if (!openssl_public_decrypt($cipheredData->hash, $hash, $publicKey)) {
            throw new EncryptionException(
                'Error while decrypting hash with public key');
        }

        if (!($data = openssl_decrypt($cipheredData->data, self::$method, $hash, 0, $cipheredData->iv))) {
            throw new EncryptionException(
                'Error while encrypting long data with encryption method');
        }

        // Check the hash
        if ($hash != $this->hash($data)) {
            throw new EncryptionException(
                'Error while comparing checksum of decrypted data');
        }
       
        return $data;
    }

    /**
     * Encrypt long data using a private key
     *
     * Since we want to encrypt long data, we encrypt the hash of the data with 
     * the private key, and we encrypt the data with self::method (eg. aes256) 
     * and the password is the encrypted hash
     *
     * @param mixed $data the data to encrypt
     * @param mixed $key  optional private key, if none given, the $this->private key is used
     *
     * @return array an array with 'data' => the encrypted data, 'hash' => the encrypted hash
     */
    public function privateEncryptLongData($data, $privateKey) : CipheredData
    {
        $hash = $this->hash($data);

        $iv = $this->createIV();

        if (!openssl_private_encrypt($hash, $hash_crypted, $privateKey)) {
            throw new EncryptionException(
                'Error while encrypting hash with private key');
        }

        $data_crypted = null;
        if (!($data_crypted = openssl_encrypt($data, self::$method, $hash, 0, $iv))) {
            throw new EncryptionException(
                'Error while encrypting long data with encryption method');
        }

        return new CipheredData($data_crypted, $hash_crypted, $iv);
    }


    /**
     * Decrypt long data using a private key
     *
     * Since we want to decrypt a long encrypted data, we decrypt the crypted hash with the private key,
     * and we decrypt the long data with the decrypted hash using encryption 
     * method
     *
     * @param mixed $data_crypted the ciphered data (ciphered with the hash)
     * @param mixed $hash_crypted the ciphered hash (ciphered with the public key)
     * @param mixed $iv           the initialization vector to increase cipher security
     * @param mixed $key          optional private key, if none given, the $this->private key is used
     *
     * @return string the decrypted data
     */
    public function privateDecryptLongData(CipheredData $cipheredData, $privateKey) : string
    {

        if (!openssl_private_decrypt($cipheredData->hash, $hash, $privateKey)) {
            throw new EncryptionException(
                'Error while decrypting hash with private key');
        }

        if (!($data = openssl_decrypt($cipheredData->data, self::$method, $hash, 0, $cipheredData->iv))) {
            throw new EncryptionException(
                'Error while encrypting long data with encryption method');
        }

        // Check the hash
        if ($hash != $this->hash($data)) {
            throw new EncryptionException(
                'Error while comparing checksum of decrypted data');
        }
       
        return $data;
    }


}


