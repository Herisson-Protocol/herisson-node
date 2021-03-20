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
            Site::PARAM_SITENAME => 'DummyName',
            Site::PARAM_EMAIL => 'dummy@example.org',
        ];
        // When
        $site = new Site($options);
        // Then
        $this->assertEquals($options[Site::PARAM_SITENAME], $site->sitename);
        $this->assertEquals($options[Site::PARAM_EMAIL], $site->email);
    }

    public function testGetSitepath()
    {
        // Given
        $options = [
            Site::PARAM_SITEURL => 'http://www.example.org',
            Site::PARAM_SITEPATH => 'bookmarks',
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