<?php


namespace Herisson\UseCase\Friend;


use Herisson\Entity\Site;
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

class FriendUseCaseTestClass extends TestCase
{
    public $fakeUrl = "http://www.example.org";
    public $fakeAlias = "Example.org";
    public $fakeContent = "<html><head><title>Vous Etes Perdu ?</title></head><body><h1>Perdu sur l'Internet ?</h1><h2>Pas de panique, on va vous aider</h2><strong><pre>    * <----- vous &ecirc;tes ici</pre></strong></body></html>";
    public $fakePublicKey;
    public $fakePrivateKey;
    public $fakeInfos = [
        Site::PARAM_SITENAME => 'HerissonTest',
        Site::PARAM_EMAIL => 'admin@herisson.io',
        Site::PARAM_VERSION => 'v0.1',
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
}