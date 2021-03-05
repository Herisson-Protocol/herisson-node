<?php

namespace Herisson\Service\Encryption;

use PHPUnit\Framework\TestCase;


class KeyPairTest extends TestCase {

    public function testKeyPairGeneration()
    {
        $key = KeyPair::generate();
        $text = "encryption content";
        $encryption = new Encryptor();
        $cipheredText = $encryption->publicEncrypt($text, $key->getPublic());
        $uncipheredText = $encryption->privateDecrypt($cipheredText, $key->getPrivate());
        $this->assertEquals($text, $uncipheredText);
        //print_r($key);
    }

    public function testKeyPairGeneration2()
    {
        $key = KeyPair::generate();
        $text = "encryption content";
        $encryption = new Encryptor();
        $cipheredText = $encryption->privateEncrypt($text, $key->getPrivate());
        $uncipheredText = $encryption->publicDecrypt($cipheredText, $key->getPublic());
        $this->assertEquals($text, $uncipheredText);
        //print_r($key);
    }
}
