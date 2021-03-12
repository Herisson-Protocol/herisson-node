<?php


namespace Herisson\UseCase\Friend;


use Herisson\Entity\Friend;
use Herisson\Repository\FriendRepository;
use Herisson\Repository\FriendRepositoryInterface;
use Herisson\Service\Encryption\Encryptor;
use Herisson\Service\Network\AbstractGrabber;
use Herisson\Service\Protocol\ProtocolException;
use Herisson\UseCase\HerissonUseCase;

class AskForFriend extends HerissonUseCase
{
    public $friendRepository;

    public function __construct(FriendRepositoryInterface $friendRepository)
    {
        $this->friendRepository = $friendRepository;
    }

    public function execute(AskForFriendRequest $request, AskForFriendResponse $response)
    {

        $friend = new Friend();
        $friend->setUrl($request->url);
        $encryptor = new Encryptor();
        $uncipheredData = $encryptor->publicDecrypt($request->signature, $request->publicKey);
        //error_log("friendAskingAction");
        $response->valid = $uncipheredData == $request->url;



    }

}