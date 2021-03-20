<?php


namespace Herisson\UseCase\Friend;


use Herisson\Entity\Friend;
use Herisson\Repository\FriendRepositoryInterface;

class AddFriend
{

    /**
     * @var FriendRepositoryInterface
     */
    public $friendRepository;

    public function __construct(FriendRepositoryInterface $friendRepository)
    {
        $this->friendRepository = $friendRepository;
    }

    public function execute(AddFriendRequest $request, AddFriendResponse $response)
    {
        $friend = new Friend();
        $friend->setAlias($request->alias);
        $friend->setUrl($request->url);
        $friendId = $this->friendRepository->save($friend);
        $response->friendId = $friendId;



    }
}