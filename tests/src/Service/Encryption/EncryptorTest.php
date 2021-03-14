<?php


namespace Herisson\Service\Encryption;

use PHPUnit\Framework\TestCase;

class EncryptorTest extends TestCase
{
    public $key;
    public $shortKeyword;
    public $longKeyword;
    public function setUp() : void
    {
        $this->key = KeyPair::generate();
        $this->shortKeyword = "Hello World";
        $this->longKeyword = str_repeat($this->shortKeyword, 100);

    }
    public function testPublicEncrypt()
    {
        // Given a keypair and a keyword

        // When the keyword is ciphered with the public key
        $encryption = new Encryptor();
        $ciphered = $encryption->publicEncrypt($this->shortKeyword, $this->key->getPublic());

        // Then we can unciphered with the private key
        $unciphered = $encryption->privateDecrypt($ciphered, $this->key->getPrivate());

        $this->assertEquals($this->shortKeyword, $unciphered);
    }

    public function testPrivateEncrypt()
    {
        // Given a keypair and a keyword

        // When the keyword is ciphered with the private key
        $encryption = new Encryptor();
        $ciphered = $encryption->privateEncrypt($this->shortKeyword, $this->key->getPrivate());

        // Then we can unciphered with the public key
        $unciphered = $encryption->publicDecrypt($ciphered, $this->key->getPublic());

        $this->assertEquals($this->shortKeyword, $unciphered);
    }


    public function testPublicEncryptWithWrongKey()
    {
        // Given a keypair and a keyword
        $keyTwo = KeyPair::generate();

        // When the keyword is ciphered with the public key
        $encryption = new Encryptor();
        $ciphered = $encryption->publicEncrypt($this->shortKeyword, $this->key->getPublic());

        // Then we can unciphered with the private key
        $this->expectException("Exception");
        $unciphered = $encryption->privateDecrypt($ciphered, $keyTwo->getPrivate());

        $this->assertEquals($this->shortKeyword, $unciphered);
    }

    public function testPrivateEncryptWithWrongKey()
    {
        // Given a second keypair
        $keyTwo = KeyPair::generate();

        // When the keyword is ciphered with the private key
        $encryption = new Encryptor();
        $ciphered = $encryption->privateEncrypt($this->shortKeyword, $this->key->getPrivate());

        $this->expectException("Exception");
        // Then we can unciphered with the public key
        $unciphered = $encryption->publicDecrypt($ciphered, $keyTwo->getPublic());

        $this->assertEquals($this->shortKeyword, $unciphered);
    }

    public function testPublicEncryptWithTooLongData()
    {
        // Given a keypair and a keyword

        // When the keyword is ciphered with the public key
        $encryption = new Encryptor();
        $this->expectException(EncryptionException::class);
        $ciphered = $encryption->publicEncrypt($this->longKeyword, $this->key->getPublic());
    }


    public function testPrivateEncryptWithTooLongData()
    {
        // Given a keypair and a keyword


        // When the keyword is ciphered with the public key
        $encryption = new Encryptor();
        $this->expectException(EncryptionException::class);
        $ciphered = $encryption->privateEncrypt($this->longKeyword, $this->key->getPrivate());
    }


    public function testPublicEncryptLongData()
    {
        // Given a keypair and a keyword

        // When the keyword is ciphered with the public key
        $encryption = new Encryptor();
        $cipheredData = $encryption->publicEncryptLongData($this->longKeyword, $this->key->getPublic());

        // Then we can unciphered with the public key
        $unciphered = $encryption->privateDecryptLongData($cipheredData, $this->key->getPrivate());

        $this->assertEquals($this->longKeyword, $unciphered);
    }


    public function testPrivateEncryptLongData()
    {
        // Given a keypair and a keyword

        // When the keyword is ciphered with the public key
        $encryption = new Encryptor();
        $cipheredData = $encryption->privateEncryptLongData($this->longKeyword, $this->key->getPrivate());

        // Then we can unciphered with the public key
        $unciphered = $encryption->publicDecryptLongData($cipheredData, $this->key->getPublic());

        $this->assertEquals($this->longKeyword, $unciphered);
    }
}