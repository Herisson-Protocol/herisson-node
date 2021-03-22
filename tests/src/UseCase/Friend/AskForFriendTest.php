<?php


namespace Herisson\UseCase\Friend;



use GuzzleHttp\Psr7\Response;
use Herisson\Entity\Friend;
use Herisson\Entity\Site;
use Herisson\Repository\FriendRepositoryMock;
use Herisson\Repository\OptionRepositoryMock;
use Herisson\Service\Encryption\Encryptor;
use Herisson\Service\Encryption\KeyPair;
use Herisson\Service\Network\Response as NetworkResponse;
use Herisson\Service\OptionLoader;

class AskForFriendTest extends FriendUseCaseTestClass
{


    public function testExecuteWithOkReponse()
    {

        // Given we have a friend in the repo
        $friend = new Friend();
        $friend->setUrl($this->fakeUrl);
        // and create request with friend id and site
        $request = new AskForFriendRequest();
        $request->friendId = $this->friendRepository->save($friend);
        $site = Site::createFromOptionRepository(new OptionRepositoryMock());
        $request->site = $site;

        // and we will receive a HTTP OK 200 response
        $responses = [
            new Response(NetworkResponse::HTTP_OK, ['Content-Type' => 'text/json'], json_encode($this->fakeInfos)),
        ];
        $friendProtocol = $this->createProtocolObject($responses);
        $response = new AskForFriendResponse();

        // When we get AskForFriend to this friend, it saved a validateByUs
        $usecase = new AskForFriend($this->friendRepository, $friendProtocol);
        $usecase->execute($request, $response);
        $httpResponse = $response->httpResponse;
        $friend = $this->friendRepository->find($response->friendId);

        $this->assertTrue($friend->getIsValidatedByUs());
        //$this->assertEquals(NetworkResponse::HTTP_OK, $httpResponse->getStatusCode());

    }
}