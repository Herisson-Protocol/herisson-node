<?php


namespace Herisson\Service\Encryption;

use PHPUnit\Framework\TestCase;

class EncryptorTest extends TestCase
{
    public function testPublicEncrypt()
    {
        // Given a keypair and a keyword
        $keyword = "hello world";
        $keyOne = KeyPair::generate();

        // When the keyword is ciphered with the public key
        $encryption = new Encryptor();
        $ciphered = $encryption->publicEncrypt($keyword, $keyOne->getPublic());

        // Then we can unciphered with the private key
        $unciphered = $encryption->privateDecrypt($ciphered, $keyOne->getPrivate());

        $this->assertEquals($keyword, $unciphered);
    }

    public function testPrivateEncrypt()
    {
        // Given a keypair and a keyword
        $keyword = "hello world";
        $keyOne = KeyPair::generate();

        // When the keyword is ciphered with the private key
        $encryption = new Encryptor();
        $ciphered = $encryption->privateEncrypt($keyword, $keyOne->getPrivate());

        // Then we can unciphered with the public key
        $unciphered = $encryption->publicDecrypt($ciphered, $keyOne->getPublic());

        $this->assertEquals($keyword, $unciphered);
    }

    /**
    public function testPrivateEncryptWithoutKey()
    {
        // Given a keypair and a keyword
        $keyword = "hello world";
        $keyOne = KeyPair::generate();

        // When the keyword is ciphered with the private key
        $encryption = new Encryptor();
        $ciphered = $encryption->privateEncrypt($keyword, $keyOne->getPrivate());

        // Then we can unciphered with the public key
        $this->expectExceptionMessage("key parameter is not a valid public key");
        $unciphered = $encryption->publicDecrypt($ciphered, "truc");

        $this->assertEquals($keyword, $unciphered);
    }
     */

}