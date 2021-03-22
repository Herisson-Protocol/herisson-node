<?php


namespace Herisson\UseCase\Friend;


use Herisson\Entity\Friend;
use Herisson\Repository\FriendRepositoryInterface;
use Herisson\Service\Encryption\Encryptor;
use Herisson\Service\Protocol\FriendProtocol;
use Herisson\UseCase\HerissonUseCase;

class AskForFriend extends HerissonUseCase
{
    public $friendRepository;
    public $friendProtocol;

    public function __construct(FriendRepositoryInterface $friendRepository, FriendProtocol $friendProtocol)
    {
        $this->friendRepository = $friendRepository;
        $this->friendProtocol = $friendProtocol;
    }

    public function execute(AskForFriendRequest $request, AskForFriendResponse $response)
    {
        $friend = $this->friendRepository->find($request->friendId);

        // Given a friend with the given URL
        $response->httpResponse = $this->friendProtocol->askForFriend($request->site, $friend);
        $response->friendId = $this->friendRepository->save($friend);

    }

}