<?php

namespace Graze\Test\Dal\NamingStrategy;

use Graze\Dal\NamingStrategy\CamelCaseNamingStrategy;
use PHPUnit_Framework_TestCase as TestCase;
use Mockery as m;
use Zend\Stdlib\Hydrator\NamingStrategy\NamingStrategyInterface;

class CamelCaseNamingStrategyTest extends TestCase
{
    /**
     * @var NamingStrategyInterface
     */
    protected $namingStrategy;

    public function setUp()
    {
        $this->namingStrategy = new CamelCaseNamingStrategy();
    }

    public function testHydrate()
    {
        $this->assertEquals('name_thingy', $this->namingStrategy->hydrate('nameThingy'));
        $this->assertEquals('some_other_text', $this->namingStrategy->hydrate('SomeOtherText'));
        $this->assertEquals('same_to_same', $this->namingStrategy->hydrate('same_to_same'));
        $this->assertEquals('weird_to_normal', $this->namingStrategy->hydrate('Weird_to_Normal'));
    }

    public function testExtract()
    {
        $this->assertEquals('nameThingy', $this->namingStrategy->extract('name_thingy'));
        $this->assertEquals('keepSame', $this->namingStrategy->extract('keepSame'));
        $this->assertEquals('fixInitialCaps', $this->namingStrategy->extract('FixInitialCaps'));
        $this->assertEquals('weirdToNormal', $this->namingStrategy->extract('weird_to_Normal'));
    }

}
