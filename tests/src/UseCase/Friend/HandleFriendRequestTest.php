<?php


namespace Herisson\UseCase\Friend;


use GuzzleHttp\Psr7\Response;
use Herisson\Entity\Friend;
use Herisson\Entity\Site;
use Herisson\Repository\OptionRepositoryMock;
use Herisson\Service\Encryption\Encryptor;
use Herisson\Service\Encryption\KeyPair;
use Herisson\Service\Network\Response as NetworkResponse;
use PHPUnit\Framework\TestCase;

class HandleFriendRequestTest extends FriendUseCaseTestClass
{
    public function testDummy()
    {
        $this->assertTrue(true);
    }


    public function testExecuteWithValidRequest()
    {
        // Given we create a friend
        $friend = new Friend();
        $friend->setUrl($this->fakeUrl);
        // and create request
        $request = new HandleFriendRequestRequest();

        // and we will receive a HTTP OK 200 response
        $responses = [
            new Response(NetworkResponse::HTTP_OK, ['Content-Type' => 'text/json'], json_encode($this->fakeInfos)),
            new Response(NetworkResponse::HTTP_OK, ['Content-Type' => 'text/html'], $this->fakePublicKey),
            /*
            new Response(NetworkResponse::HTTP_OK, ['Content-Type' => 'text/json'], "OK"),
            //new Response(NetworkResponse::HTTP_ACCEPTED, ['Content-Type' => 'text/html'], $this->fakePublicKey),
            */
        ];
        $friendProtocol = $this->createProtocolObject($responses);
        $encryptor = new Encryptor();
        $request->signature = $encryptor->privateEncrypt($this->fakeUrl, $this->fakePrivateKey);
        $request->url = $this->fakeUrl;
        $response = new HandleFriendRequestResponse();
        $usecase = new HandleFriendRequest($this->friendRepository, $friendProtocol);
        $usecase->execute($request, $response);
        $httpResponse = $response->httpResponse;
        $this->assertEquals(NetworkResponse::HTTP_OK, $httpResponse->getStatusCode());

    }


    public function testExecuteWithNotValidRequest()
    {
        $otherKey = KeyPair::generate();
        // Given we create a friend
        $friend = new Friend();
        $friend->setUrl($this->fakeUrl);
        // and create request
        $request = new HandleFriendRequestRequest();

        // and we will receive a HTTP OK 200 response
        $responses = [
            new Response(NetworkResponse::HTTP_OK, ['Content-Type' => 'text/json'], json_encode($this->fakeInfos)),
            new Response(NetworkResponse::HTTP_OK, ['Content-Type' => 'text/html'], $otherKey->getPublic()),
            /*
            new Response(NetworkResponse::HTTP_OK, ['Content-Type' => 'text/json'], "OK"),
            //new Response(NetworkResponse::HTTP_ACCEPTED, ['Content-Type' => 'text/html'], $this->fakePublicKey),
            */
        ];
        $friendProtocol = $this->createProtocolObject($responses);
        $encryptor = new Encryptor();
        $request->signature = $encryptor->privateEncrypt($this->fakeUrl, $this->fakePrivateKey);
        $request->url = $this->fakeUrl;
        $response = new HandleFriendRequestResponse();
        $usecase = new HandleFriendRequest($this->friendRepository, $friendProtocol);
        $usecase->execute($request, $response);
        $httpResponse = $response->httpResponse;
        $this->assertEquals(NetworkResponse::HTTP_EXPECTATION_FAILED, $httpResponse->getStatusCode());

    }
}