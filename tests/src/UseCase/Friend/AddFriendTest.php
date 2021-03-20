<?php


namespace Herisson\UseCase\Friend;


use Herisson\Repository\FriendRepositoryMock;

class AddFriendTest extends FriendUseCaseTestClass
{


    public function testExecute()
    {
        $request = new AddFriendRequest();
        $request->url = $this->fakeUrl;
        $request->alias = $this->fakeAlias;
        $response = new AddFriendResponse();
        $usecase = new AddFriend($this->friendRepository);
        $usecase->execute($request, $response);
        $friend = $this->friendRepository->find($response->friendId);
        $this->assertEquals($this->fakeUrl, $friend->getUrl());
        $this->assertEquals($this->fakeAlias, $friend->getAlias());

    }

}