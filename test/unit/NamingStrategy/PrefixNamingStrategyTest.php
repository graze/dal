<?php

namespace Graze\Test\Dal\NamingStrategy;

use Graze\Dal\NamingStrategy\PrefixNamingStrategy;

use PHPUnit_Framework_TestCase as TestCase;
use Mockery as m;

class PrefixNamingStrategyTest extends TestCase
{
    protected $namingStrategy;
    
    public function setUp()
    {
        $this->namingStrategy = new PrefixNamingStrategy('st_');
    }
    
    public function tearDown()
    {
        $this->namingStrategy = null;
    }
    
    public function testHydrate()
    {    
        $hydrate = $this->namingStrategy->hydrate('test');
        
        $this->assertEquals('st_test', $hydrate);
    }
    
    public function testExtract()
    {    
        $this->assertEquals('banana', $this->namingStrategy->extract('st_banana'));
        $this->assertEquals('banana', $this->namingStrategy->extract('banana'));
        $this->assertEquals('st_banana', $this->namingStrategy->extract('st_st_banana'));
        $this->assertEquals('banana_st', $this->namingStrategy->extract('banana_st'));
    }
}
