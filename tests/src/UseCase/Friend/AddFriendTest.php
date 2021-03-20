<?php


namespace Herisson\UseCase\Friend;


use Herisson\Repository\FriendRepositoryMock;
use PHPUnit\Framework\TestCase;

class AddFriendTest extends TestCase
{
    public $fakeUrl = "http://www.example.org";
    public $fakeAlias = "Example.org";
    public $repo;
    public function setUp() : void
    {
        $this->repo = new FriendRepositoryMock();

    }

    public function testNominal()
    {
        $request = new AddFriendRequest();
        $request->url = $this->fakeUrl;
        $request->alias = $this->fakeAlias;
        $response = new AddFriendResponse();
        $usecase = new AddFriend($this->repo);
        $usecase->execute($request, $response);
        $friend = $this->repo->find($response->friendId);
        $this->assertEquals($this->fakeUrl, $friend->getUrl());
        $this->assertEquals($this->fakeAlias, $friend->getAlias());

    }

}