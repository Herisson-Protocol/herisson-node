<?php

namespace Herisson\Service;

use Herisson\Repository\OptionRepository;
use Herisson\Repository\OptionRepositoryMock;
use Herisson\Service\OptionLoader;

class OptionLoaderTest extends \PHPUnit\Framework\TestCase
{
    public $repository;

    public function setUp() : void
    {
        $this->repository = new OptionRepositoryMock();
    }

    public function testConstruct()
    {
        $optionLoader = new OptionLoader($this->repository);
        $this->assertTrue($optionLoader instanceof OptionLoader);

    }

    public function testLoad()
    {
        $optionLoader = new OptionLoader($this->repository);
        $options = $optionLoader->load(['sitename', 'email']);
        $this->assertEquals("HerissonSite", $options['sitename']);
        $this->assertEquals("admin@example.org", $options['email']);
    }

}