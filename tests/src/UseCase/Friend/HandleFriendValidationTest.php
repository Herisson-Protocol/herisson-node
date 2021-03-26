<?php


namespace Herisson\UseCase\Friend;


use GuzzleHttp\Psr7\Response;
use Herisson\Entity\Friend;
use Herisson\Service\Encryption\Encryptor;
use Herisson\Service\Network\Response as NetworkResponse;

class HandleFriendValidationTest extends FriendUseCaseTestClass
{

    public function testExecute()
    {
        // Given we create a friend
        $friend = new Friend();
        $friend->setUrl($this->fakeUrl);
        // and create request
        $request = new HandleFriendValidationRequest();

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
        $response = new HandleFriendValidationResponse();
        $usecase = new HandleFriendValidation($this->friendRepository, $friendProtocol);
        $usecase->execute($request, $response);
        $httpResponse = $response->httpResponse;
        $this->assertEquals(NetworkResponse::HTTP_OK, $httpResponse->getStatusCode());
    }
}