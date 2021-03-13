<?php


namespace Herisson\Entity;


use Doctrine\Persistence\ObjectManager;
use GuzzleHttp\Psr7\Response;
use Herisson\Service\Message;
use Herisson\Service\Network\GrabberGuzzleMock;
use PHPUnit\Framework\TestCase;

class BookmarkTest extends TestCase
{
    public function testHash()
    {
        // Given
        $url = "http://www.herisson.io";
        $hash = md5($url);
        // When
        $bookmark = new Bookmark();
        $bookmark->setUrl($url);
        //$bookmark->setContent("content");
        // Then
        $this->assertEquals($hash, $bookmark->getHash());
    }

    public function dataProviderFaviconUrl() : array
    {
        return [
            ["http://www.herisson.io", "http://www.herisson.io/favicon.ico"],
            ["http://www.google.com", "http://www.google.com/favicon.ico"],
            ["http://www.names.co.uk", "http://www.names.co.uk/favicon.ico"],
        ];
    }

    /**
     * @dataProvider dataProviderFaviconUrl
     */
    public function testFaviconUrl($url, $faviconUrl)
    {
        // Given
        $bookmark = new Bookmark();
        // When
        $bookmark->setUrl($url);
        //$bookmark->setContent("content");
        // Then
        $this->assertEquals($faviconUrl, $bookmark->getFaviconUrl());
    }

    /*
    public function testPersist()
    {
        // Given
        $url = "http://www.herisson.io";
        $hash = md5($url);
        // When
        $bookmark = new Bookmark();
        $bookmark->setUrl($url);
        //$bookmark->setContent("content");
        $objectManager = $this->createMock(ObjectManager::class);
        $objectManager->persist($bookmark);
        // Then
        //$this->assertEquals($hash, $bookmark->getHash());
    }
    */



}