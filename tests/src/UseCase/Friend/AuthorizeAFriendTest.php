<?php


namespace Herisson\UseCase\Friend;


use GuzzleHttp\Psr7\Response;
use Herisson\Entity\Friend;
use Herisson\Entity\Site;
use Herisson\Repository\OptionRepositoryMock;
use Herisson\Service\Network\Response as NetworkResponse;

class AuthorizeAFriendTest extends FriendUseCaseTestClass
{

    public function testExecute()
    {
        // Given we have a friend in the repo, that has been validated by Him (because it has been requested)
        $friend = new Friend();
        $friend->setUrl($this->fakeUrl);
        $friend->setIsValidatedByHim(true);
        // and create request with friend id and site
        $request = new AuthorizeAFriendRequest();
        $request->friendId = $this->friendRepository->save($friend);
        $site = Site::createFromOptionRepository(new OptionRepositoryMock());
        $request->site = $site;

        // and we will receive a HTTP OK 200 response
        $responses = [
            new Response(NetworkResponse::HTTP_OK, ['Content-Type' => 'text/json'], json_encode($this->fakeInfos)),
        ];
        $friendProtocol = $this->createProtocolObject($responses);
        $response = new AuthorizeAFriendResponse();

        // When we get AskForFriend to this friend, it saved a validateByUs
        $usecase = new AuthorizeAFriend($this->friendRepository, $friendProtocol);
        $usecase->execute($request, $response);
        $httpResponse = $response->httpResponse;
        $friend = $this->friendRepository->find($response->friendId);

        $this->assertTrue($friend->getIsValidatedByUs());
        $this->assertEquals(NetworkResponse::HTTP_OK, $httpResponse->getStatusCode());


    }
}