<?php


namespace Herisson\UseCase\Friend;


use Herisson\Repository\FriendRepositoryInterface;
use Herisson\Service\Protocol\FriendProtocol;

class LoadFriendInfos
{

    public $friendRepository;
    public $friendProtocol;

    public function __construct(FriendRepositoryInterface $friendRepository, /*GrabberInterface $grabber,*/ FriendProtocol $friendProtocol)
    {
        $this->friendRepository = $friendRepository;
        $this->friendProtocol = $friendProtocol;
    }

    public function execute(LoadFriendInfosRequest $request, LoadFriendInfosResponse $response)
    {
        $friend = $this->friendRepository->find($request->friendId);
        $this->friendProtocol->getInfo($friend);
        $this->friendProtocol->reloadPublicKey($friend);
        $friendId = $this->friendRepository->save($friend);
        $response->friendId = $friendId;
    }
}