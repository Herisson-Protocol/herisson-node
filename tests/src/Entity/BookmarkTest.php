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



    public function dataProviderUrlPath() : array
    {
        return [
            ["http://www.herisson.io/index.php", "index.php"],
            ["http://www.herisson.io/", "index.html"],
            ["http://www.herisson.io", "index.html"],
            ["http://www.google.com/index.html", "index.html"],
            ["http://www.google.com/Truc.PHP", "Truc.PHP"],
            ["http://www.names.co.uk/foo/bar.asp", "foo/bar.asp"],
            ["http://www.names.co.uk/foo/very-long-file-name-that-should-not-break-anything", "foo/very-long-file-name-that-should-not-break-anything"],
        ];
    }

    /**
     * @dataProvider dataProviderUrlPath
     */
    public function testGetUrlPath($url, $expectedPath)
    {
        // Given
        $bookmark = new Bookmark();
        // When
        $bookmark->setUrl($url);
        //$bookmark->setContent("content");
        // Then
        $this->assertEquals($expectedPath, $bookmark->getUrlPath());
    }


    public function dataProviderGetDir() : array
    {
        return [
            [
                "http://www.herisson.io",
                "/tmp/data",
                "/tmp/data/b/b0/b00bb656034239d2e23964458a7c3808",
                "b/b0/b00bb656034239d2e23964458a7c3808"
            ],
            [
                "http://www.herisson.io",
                "/tmp/data/",
                "/tmp/data/b/b0/b00bb656034239d2e23964458a7c3808",
                "b/b0/b00bb656034239d2e23964458a7c3808"
            ],
            [
                "http://www.herisson.io/",
                "/tmp/data",
                "/tmp/data/e/e6/e6f5072bae7801389d41a9857f2ef422",
                "e/e6/e6f5072bae7801389d41a9857f2ef422"
            ],
            [
                "http://www.herisson.io/",
                "////tmp////data////",
                "/tmp/data/e/e6/e6f5072bae7801389d41a9857f2ef422",
                "e/e6/e6f5072bae7801389d41a9857f2ef422"
            ],
            [
                "http://www.herisson.io/index.html",
                "/tmp/data",
                "/tmp/data/1/1f/1fe8553970fc218a20fb53da175fbd3d",
                "1/1f/1fe8553970fc218a20fb53da175fbd3d"
            ],
        ];
    }

    /**
     * @dataProvider dataProviderGetDir
     */
    public function testGetDir($url, $exportDir, $fullExpectedDir, $expectedDir)
    {
        // Given
        $bookmark = new Bookmark();
        // When
        $bookmark->setUrl($url);
        // Then
        $this->assertEquals($fullExpectedDir, $bookmark->getDir($exportDir));
    }


    /**
     * @dataProvider dataProviderGetDir
     */
    public function testGetHashDir($url, $exportDir, $fullExpectedDir, $expectedDir)
    {
        // Given
        $bookmark = new Bookmark();
        // When
        $bookmark->setUrl($url);
        // Then
        $this->assertEquals($expectedDir, $bookmark->getHashDir());
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