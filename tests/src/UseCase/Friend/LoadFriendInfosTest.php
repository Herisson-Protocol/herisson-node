<?php


namespace Herisson\UseCase\Friend;


use GuzzleHttp\Psr7\Response;
use Herisson\Entity\Friend;
use Herisson\Repository\FriendRepositoryInterface;
use Herisson\Repository\FriendRepositoryMock;
use Herisson\Repository\OptionRepositoryMock;
use Herisson\Service\Encryption\Encryptor;
use Herisson\Service\Encryption\KeyPair;
use Herisson\Service\Message;
use Herisson\Service\Network\GrabberGuzzleMock;
use Herisson\Service\Network\GrabberInterface;
use Herisson\Service\OptionLoader;
use Herisson\Service\Protocol\FriendProtocol;
use PHPUnit\Framework\TestCase;

class LoadFriendInfosTest extends TestCase
{
    public $fakeUrl = "http://www.example.org";
    public $fakeAlias = "Example.org";
    public $fakeContent = "<html><head><title>Vous Etes Perdu ?</title></head><body><h1>Perdu sur l'Internet ?</h1><h2>Pas de panique, on va vous aider</h2><strong><pre>    * <----- vous &ecirc;tes ici</pre></strong></body></html>";
    public $fakePublicKey;
    public $fakePrivateKey;
    public $fakeInfos = [
        'sitename' => 'HerissonTest',
        'adminEmail' => 'admin@herisson.io',
        'version' => 'v0.1',
    ];

    /**
     * @var FriendRepositoryInterface
     */
    public $friendRepository;
    /**
     * @var GrabberInterface
     */
    public $grabber;

    public function setUp() : void
    {
        $this->friendRepository = new FriendRepositoryMock();
        $this->grabber = new GrabberGuzzleMock(new Message());
        $key = KeyPair::generate();
        $this->fakePublicKey = $key->getPublic();
        $this->fakePrivateKey = $key->getPrivate();

    }


    public function createProtocolObject(array $responses = null) : FriendProtocol
    {
        $optionRepository = new OptionRepositoryMock();
        $optionLoader = new OptionLoader($optionRepository);
        $encryptor = new Encryptor();
        $message = new Message();
        $grabber = new GrabberGuzzleMock($message);
        if ($responses) {
            $grabber->setResponses($responses);
        }
        return new FriendProtocol($optionLoader, $encryptor, $grabber, $message);
    }

    public function testNominal()
    {
        // Given
        $responses = [
            new Response(200, ['Content-Type' => 'text/json'], json_encode($this->fakeInfos)),
            new Response(200, ['Content-Type' => 'text/html'], $this->fakePublicKey),
        ];
        $protocol = $this->createProtocolObject($responses);
        $request = new LoadFriendInfosRequest();
        $friend = new Friend();
        $friend->setUrl($this->fakeUrl);
        $request->friendId = $this->friendRepository->save($friend);
        $response = new LoadFriendInfosResponse();
        // When
        $usecase = new LoadFriendInfos($this->friendRepository, $protocol);
        $usecase->execute($request, $response);
        // Then
        $friend = $this->friendRepository->find($response->friendId);
        $this->assertEquals($this->fakeInfos['adminEmail'], $friend->getEmail());
        $this->assertEquals($this->fakeInfos['sitename'], $friend->getName());
        $this->assertEquals($this->fakePublicKey, $friend->getPublicKey());
        $this->assertEquals($request->friendId, $response->friendId);

    }



}