<?php


namespace Herisson\Entity;


use Herisson\Repository\OptionRepositoryMock;
use Herisson\Service\OptionLoader;
use PHPUnit\Framework\TestCase;

class SiteTest extends TestCase
{

    public function testCreate()
    {
        // Given
        $options = [
            'sitename' => 'DummyName',
            'email' => 'dummy@example.org',
        ];
        // When
        $site = new Site($options);
        // Then
        $this->assertEquals($options['sitename'], $site->sitename);
        $this->assertEquals($options['email'], $site->email);
    }

    public function testGetSitepath()
    {
        // Given
        $options = [
            'siteurl' => 'http://www.example.org',
            'sitepath' => 'bookmarks',
        ];
        $sitePath = 'http://www.example.org/bookmarks';
        // When
        $site = new Site($options);
        // Then
        $this->assertEquals($sitePath, $site->getFullSitepath());
    }


    public function testCreateFromOptionLoader()
    {
        // Given
        $optionLoader = new OptionLoader(new OptionRepositoryMock());
        // When
        $site = Site::createFromOptionLoader($optionLoader);
        // Then
        $this->assertEquals("HerissonSite", $site->sitename);
        $this->assertEquals("admin@example.org", $site->email);
    }

}