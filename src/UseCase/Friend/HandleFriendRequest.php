<?php


namespace Herisson\UseCase\Friend;

use Herisson\Entity\Friend;
use Herisson\Repository\FriendRepositoryInterface;
use Herisson\Service\Encryption\EncryptionException;
use Herisson\Service\Encryption\Encryptor;
use Herisson\Service\Network\Response;
use Herisson\Service\Protocol\FriendProtocol;
use Herisson\UseCase\HerissonUseCase;

class HandleFriendRequest extends HerissonUseCase
{
    public $friendRepository;
    public $friendProtocol;

    public function __construct(FriendRepositoryInterface $friendRepository, FriendProtocol $friendProtocol)
    {
        $this->friendRepository = $friendRepository;
        $this->friendProtocol = $friendProtocol;
    }


    public function execute(HandleFriendRequestRequest $request, HandleFriendRequestResponse $response)
    {
        //$friend = $this->friendRepository->find($request->friendId);
        $friend = new Friend();
        $friend->setUrl($request->url);
        //$request->friendId =
        $lfirequest = new LoadFriendInfosRequest();
        $lfirequest->friendId = $this->friendRepository->save($friend);
        $lfiresponse = new LoadFriendInfosResponse();
        // When

        $usecase = new LoadFriendInfos($this->friendRepository, $this->friendProtocol);
        $usecase->execute($lfirequest, $lfiresponse);
        // Then
        $friend = $this->friendRepository->find($lfiresponse->friendId);


        // Given a friend with the given URL
        //$friend = new Friend();
        //$friend->setUrl($request->url);
        try {

            $encryptor = new Encryptor();
            $uncipheredData = $encryptor->publicDecrypt($request->signature, $friend->getPublicKey());
            if($uncipheredData == $request->url) {
                $response->httpResponse = new Response("OK", Response::HTTP_OK, []);
            } else {
                $response->httpResponse = new Response("OK", Response::HTTP_EXPECTATION_FAILED, []);
            }
        } catch (EncryptionException $exp) {
            $response->httpResponse = new Response("OK", Response::HTTP_EXPECTATION_FAILED, []);
        }
            //$this->friendProtocol->askForFriend($request->site, $friend);
        //$this->friendRepository->find($request->friendId);
        //$response->friendId = $friend->getId();
        //$response->valid = $uncipheredData == $request->url;
        //return $response;

    }
}