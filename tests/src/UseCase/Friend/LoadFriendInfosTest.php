<?php


namespace Herisson\UseCase\Friend;


use GuzzleHttp\Psr7\Response;
use Herisson\Entity\Site;
use Herisson\Service\Network\Response as NetworkResponse;
use Herisson\Entity\Friend;

class LoadFriendInfosTest extends FriendUseCaseTestClass
{

    public function testExecute()
    {
        // Given
        $responses = [
            new Response(NetworkResponse::HTTP_OK, ['Content-Type' => 'text/json'], json_encode($this->fakeInfos)),
            new Response(NetworkResponse::HTTP_OK, ['Content-Type' => 'text/html'], $this->fakePublicKey),
        ];
        $protocol = $this->createProtocolObject($responses);
        $request = new LoadFriendInfosRequest();
        $friend = new Friend();
        $friend->setUrl($this->fakeUrl);
        $request->friendId = $this->friendRepository->save($friend);
        $response = new LoadFriendInfosResponse();
        // When
        $usecase = new LoadFriendInfos($this->friendRepository, $protocol);
        $usecase->execute($request, $response);
        // Then
        $friend = $this->friendRepository->find($response->friendId);
        $this->assertEquals($this->fakeInfos[Site::PARAM_EMAIL], $friend->getEmail());
        $this->assertEquals($this->fakeInfos[Site::PARAM_SITENAME], $friend->getName());
        $this->assertEquals($this->fakePublicKey, $friend->getPublicKey());
        $this->assertEquals($request->friendId, $response->friendId);

    }



}