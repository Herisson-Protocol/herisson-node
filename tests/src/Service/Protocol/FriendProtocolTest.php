<?php


namespace Herisson\Service\Protocol;


use Herisson\Entity\Friend;
use Herisson\Entity\Site;
use Herisson\Repository\OptionRepository;
use Herisson\Repository\OptionRepositoryMock;
use Herisson\Service\Encryption\Encryptor;
use Herisson\Service\Encryption\KeyPair;
use Herisson\Service\Message;
use Herisson\Service\Network\GrabberGuzzleMock;
use Herisson\Service\OptionLoader;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Response;

class FriendProtocolTest extends TestCase
{
    private $publicKey;
    private $infos;

    public function setUp() :void
    {
        $this->publicKey = "-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA3BqOjNxRfoilKNNvASi7
IhnrVNqP34PThXT3QXZ4E+Sv7RrTKvkyxFBQwt9g045Pftq9+ZViRf2KoyilLyH7
t9yh62nrFy1FfZlTo2jwhX5IqpdlclWKsGUy3ZdQ8OXDdgEPVqrOAl4449aawrNF
CnHI64AqidcOYRqgt9lppCppxJ/giXy4Abw+dJ9eT1bOe/RosPufz9j3/t3Mcj8b
grVH2JiLlbg/UOeKNivbQQ0JR1fU1AS/zoCu6foTcCoiXblxFVq+wBLV3xLPvY7O
2kCnQC8vaCg5/U3szL/FQKmqMeQDJ2JYGTU7swwj4pSZP5MfX/6St1LPG31JGl7K
cQIDAQAB
-----END PUBLIC KEY-----";
        $this->infos = [
            Site::PARAM_SITENAME => 'HerissonTest',
            Site::PARAM_EMAIL => 'admin@herisson.io',
            Site::PARAM_VERSION => 'v0.1',
        ];
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

    public function testReloadPublicKey()
    {
        // Given
        $responses = [
            new Response(200, ['Content-Type' => 'text/html'], $this->publicKey),
        ];
        $friendProtocol = $this->createProtocolObject($responses);
        $friend = new Friend();
        $friend->setUrl('http://thailande.taillandier.name/bookmarks/');
        // When
        $friendProtocol->reloadPublicKey($friend);
        // Then
        $this->assertEquals($this->publicKey, $friend->getPublicKey());
    }


    public function testGetInfo()
    {
        // Given
        $responses = [
            new Response(200, ['Content-Type' => 'text/html'], json_encode($this->infos)),
        ];
        $friendProtocol = $this->createProtocolObject($responses);
        $friend = new Friend();
        $friend->setUrl('http://thailande.taillandier.name/bookmarks/');
        // When
        $friendProtocol->getInfo($friend);
        // Then
        $this->assertEquals($this->infos[Site::PARAM_EMAIL], $friend->getEmail());
        $this->assertEquals($this->infos[Site::PARAM_SITENAME], $friend->getName());
    }

    public function createSiteObject() : Site
    {
        $key = KeyPair::generate();
        $options = [
            Site::PARAM_PUBLICKEY => $key->getPublic(),
            Site::PARAM_PRIVATEKEY => $key->getPrivate(),
            Site::PARAM_SITEURL => 'http://localhost:8000',
            Site::PARAM_SITEPATH => '/',
            Site::PARAM_EMAIL => 'admin@example.org',
            Site::PARAM_SITENAME => 'Example.org',
        ];
        return new Site($options);
    }

    public function testAskWaitingValidation()
    {
        // Given
        $responses = [
            new Response(200, ['Content-Type' => 'text/html'], '1'),
        ];
        $friendProtocol = $this->createProtocolObject($responses);
        $site = $this->createSiteObject();
        $friend = new Friend();
        // When
        $friendProtocol->askForFriend($site, $friend);
        // Then
        $this->assertTrue($friend->getIsValidatedByUs());
        $this->assertFalse($friend->getIsValidatedByHim());
        $this->assertFalse($friend->getIsActive());
    }


    public function testAskWithAutomaticValidation()
    {
        // Given
        $responses = [
            new Response(202, ['Content-Type' => 'text/html'], '1'),
        ];
        $friendProtocol = $this->createProtocolObject($responses);
        $site = $this->createSiteObject();
        $friend = new Friend();
        // When
        $friendProtocol->askForFriend($site, $friend);
        // Then
        $this->assertTrue($friend->getIsValidatedByUs());
        $this->assertTrue($friend->getIsValidatedByHim());
        $this->assertTrue($friend->getIsActive());
    }

    public function testAskWithKeyProblems()
    {
        // Given
        $responses = [
            new Response(417, ['Content-Type' => 'text/html'], '1'),
        ];
        $friendProtocol = $this->createProtocolObject($responses);
        $site = $this->createSiteObject();
        $friend = new Friend();
        // When
        $friendProtocol->askForFriend($site, $friend);
        // Then
        $this->assertFalse($friend->getIsValidatedByHim());
        $this->assertFalse($friend->getIsActive());
    }


    public function testAutorizeFriendRequest()
    {
        // Given
        $responses = [
            new Response(200, ['Content-Type' => 'text/html'], '1'),
        ];
        $friendProtocol = $this->createProtocolObject($responses);
        $site = $this->createSiteObject();
        $friend = new Friend();
        $friend->setIsValidatedByHim(true);
        // When
        $friendProtocol->autorizeFriendRequest($site, $friend);
        // Then
        $this->assertTrue($friend->getIsValidatedByUs());
        $this->assertTrue($friend->getIsActive());
    }

    public function testHandleFriendValidationOk()
    {
        // Given
        $key = KeyPair::generate();
        $encryptor = new Encryptor();
        $url = "http://dummy";
        $signature = $encryptor->privateEncrypt($url, $key->getPrivate());
        $responses = [
            new Response(200, ['Content-Type' => 'text/html'], $key->getPublic()),
        ];
        $friendProtocol = $this->createProtocolObject($responses);
        $site = $this->createSiteObject();

        // When
        $friend = new Friend();
        $friend->setUrl($url);
        $response = $friendProtocol->handleFriendValidation($site, $friend, $signature);

        //Then
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertFalse($friend->getIsValidatedByHim());
        $this->assertFalse($friend->getIsActive());
    }


    public function testHandleFriendValidationNok()
    {
        $responses = [
            new Response(200, ['Content-Type' => 'text/html'], $this->publicKey),
        ];
        $friendProtocol = $this->createProtocolObject($responses);
        $site = $this->createSiteObject();
        $friend = new Friend();
        $response = $friendProtocol->handleFriendValidation($site, $friend, "");
        $this->assertEquals(417, $response->getStatusCode());
        $this->assertFalse($friend->getIsValidatedByHim());
        $this->assertFalse($friend->getIsActive());
    }


    /*
        public function testCheckFriendValidity()
        {
            $responses = [
                new Response(202, ['Content-Type' => 'text/html'], '1'),
            ];
            $friendProtocol = $this->createProtocolObject($responses);
            $site = $this->createSiteObject();
            $friend = new Friend();
    //        $friend->setUrl('http://thailande.taillandier.name/bookmarks/');
            $friend->setUrl('http://localhost:8001');
            $key = KeyPair::generate();
            //$encryptor = new Encryptor();
            $friend->setPublicKey($key->getPublic());
            //$encryptedSiteName = $encryptor->privateDecrypt("sitename", $key->getPrivate());
            $friendProtocol->checkFriendValidity($site, $friend);

        }
    */


}