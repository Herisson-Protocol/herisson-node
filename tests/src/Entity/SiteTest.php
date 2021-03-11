<?php


namespace Herisson\Entity;


use PHPUnit\Framework\TestCase;

class SiteTest extends TestCase
{

    public function testDummy()
    {
        $this->assertTrue(true);
    }

    public function testCreate()
    {
        // Given
        $options = [
            'name' => 'DummyName',
            'email' => 'dummy@example.org',
        ];
        // When
        $site = new Site($options);
        // Then
        $this->assertEquals($options['name'], $site->name);
        $this->assertEquals($options['email'], $site->email);
    }

    public function testGetSite()
    {
        // Given
        $options = [
            'siteurl' => 'http://www.example.org',
            'basePath' => 'bookmarks',
        ];
        $sitePath = 'http://www.example.org/bookmarks';
        // When
        $site = new Site($options);
        // Then
        $this->assertEquals($sitePath, $site->getSitePath());
    }
}