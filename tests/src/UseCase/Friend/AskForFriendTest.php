<?php


namespace Herisson\UseCase\Friend;



use Herisson\Repository\FriendRepositoryMock;
use Herisson\Service\Encryption\Encryptor;
use Herisson\Service\Encryption\KeyPair;

class AskForFriendTest extends FriendUseCaseTestClass
{


    public function testExecute()
    {
        $request = new AskForFriendRequest();
        $encryptor = new Encryptor();
        $request->signature = $encryptor->privateEncrypt($this->fakeUrl, $this->fakePrivateKey);
        $request->url = $this->fakeUrl;
        $request->publicKey = $this->fakePublicKey;
        $response = new AskForFriendResponse();
        $usecase = new AskForFriend($this->friendRepository);
        $usecase->execute($request, $response);
        $this->assertTrue($response->valid);

    }
}