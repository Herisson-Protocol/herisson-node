<?php


namespace Herisson\UseCase\Friend;


use Herisson\Repository\FriendRepositoryInterface;
use Herisson\Service\Protocol\FriendProtocol;

class AuthorizeAFriend
{
    public $friendRepository;
    public $friendProtocol;


    public function __construct(FriendRepositoryInterface $friendRepository, FriendProtocol $friendProtocol)
    {
        $this->friendRepository = $friendRepository;
        $this->friendProtocol = $friendProtocol;
    }

    public function execute(AuthorizeAFriendRequest $request, AuthorizeAFriendResponse $response)
    {
        $friend = $this->friendRepository->find($request->friendId);

        // Given a friend with the given URL
        $response->httpResponse = $this->friendProtocol->authorizeFriendRequest($request->site, $friend);
        $response->friendId = $this->friendRepository->save($friend);
    }
}