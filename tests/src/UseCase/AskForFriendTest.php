<?php


namespace Herisson\UseCase;



use Herisson\Repository\FriendRepositoryMock;
use Herisson\Service\Encryption\Encryptor;
use Herisson\Service\Encryption\KeyPair;
use Herisson\UseCase\Friend\AskForFriend;
use Herisson\UseCase\Friend\AskForFriendRequest;
use Herisson\UseCase\Friend\AskForFriendResponse;
use PHPUnit\Framework\TestCase;

class AskForFriendTest extends TestCase
{

    public function testDummy()
    {
        $this->assertTrue(true);
    }

    public function testExecute()
    {
        $key = KeyPair::generate();
        $request = new AskForFriendRequest();
        $encryptor = new Encryptor();
        $url = "http://test.example.org";
        $request->signature = $encryptor->privateEncrypt($url, $key->getPrivate());
        $request->url = $url;
        $request->publicKey = $key->getPublic();
        $response = new AskForFriendResponse();
        $usecase = new AskForFriend(new FriendRepositoryMock());
        $usecase->execute($request, $response);
        $this->assertTrue($response->valid);

    }
    /*
    */
}