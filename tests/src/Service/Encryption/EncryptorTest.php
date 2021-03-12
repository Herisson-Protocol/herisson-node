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


    public function testPublicEncryptWithWrongKey()
    {
        // Given a keypair and a keyword
        $keyword = "hello world";
        $keyOne = KeyPair::generate();
        $keyTwo = KeyPair::generate();

        // When the keyword is ciphered with the public key
        $encryption = new Encryptor();
        $ciphered = $encryption->publicEncrypt($keyword, $keyOne->getPublic());

        // Then we can unciphered with the private key
        $this->expectException("Exception");
        $unciphered = $encryption->privateDecrypt($ciphered, $keyTwo->getPrivate());

        $this->assertEquals($keyword, $unciphered);
    }

    public function testPrivateEncryptWithWrongKey()
    {
        // Given a keypair and a keyword
        $keyword = "hello world";
        $keyOne = KeyPair::generate();
        $keyTwo = KeyPair::generate();

        // When the keyword is ciphered with the private key
        $encryption = new Encryptor();
        $ciphered = $encryption->privateEncrypt($keyword, $keyOne->getPrivate());

        $this->expectException("Exception");
        // Then we can unciphered with the public key
        $unciphered = $encryption->publicDecrypt($ciphered, $keyTwo->getPublic());

        $this->assertEquals($keyword, $unciphered);
    }


}