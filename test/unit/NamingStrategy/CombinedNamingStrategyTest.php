<?php

namespace Graze\Test\Dal\NamingStrategy;

use Graze\Dal\NamingStrategy\CombinedNamingStrategy;
use Mockery as m;
use PHPUnit_Framework_TestCase as TestCase;

class CombinedNamingStrategyTest extends TestCase
{
    /**
     * @var CombinedNamingStrategy
     */
    protected $namingStrategy;

    public function setUp()
    {
        $this->namingStrategy = new CombinedNamingStrategy();
    }

    public function testAddStrategy()
    {
        $strategy = m::mock('Zend\Stdlib\Hydrator\NamingStrategy\NamingStrategyInterface');

        $this->namingStrategy->addNamingStrategy($strategy, 1);

        $this->assertTrue($this->namingStrategy->hasNamingStrategy($strategy));

        $strategy->shouldReceive('hydrate')->with('test', null)->andReturn('testBack');
        $strategy->shouldReceive('extract')->with('testBack', null)->andReturn('test');

        $this->assertEquals('testBack', $this->namingStrategy->hydrate('test'));
        $this->assertEquals('test', $this->namingStrategy->extract('testBack'));
    }

    public function testRemoveStrategy()
    {
        $strategy = m::mock('Zend\Stdlib\Hydrator\NamingStrategy\NamingStrategyInterface');

        $this->assertFalse($this->namingStrategy->removeNamingStrategy($strategy));

        $this->namingStrategy->addNamingStrategy($strategy);

        $this->assertTrue($this->namingStrategy->hasNamingStrategy($strategy));

        $this->assertTrue($this->namingStrategy->removeNamingStrategy($strategy));
    }

    public function testAddingMultipleStrategies()
    {
        $strategy1 = m::mock('Zend\Stdlib\Hydrator\NamingStrategy\NamingStrategyInterface');
        $strategy2 = m::mock('Zend\Stdlib\Hydrator\NamingStrategy\NamingStrategyInterface');

        $strategy1->shouldReceive('hydrate')->with('first', null)->andReturn('second');
        $strategy2->shouldReceive('hydrate')->with('second', null)->andReturn('third');
        $strategy1->shouldReceive('extract')->with('first_E', null)->andReturn('second_E');
        $strategy2->shouldReceive('extract')->with('second_E', null)->andReturn('third_E');

        $this->assertTrue($this->namingStrategy->addNamingStrategy($strategy1, 1), 'Failed to add the first strategy');
        $this->assertTrue($this->namingStrategy->addNamingStrategy($strategy2, 2), 'Failed to add the second strategy');

        $this->assertEquals('third', $this->namingStrategy->hydrate('first'));
        $this->assertEquals('third_E', $this->namingStrategy->extract('first_E'));
    }

    public function testAddingMultipleStrategiesInDifferentOrderUsesPriority()
    {

        $strategy1 = m::mock('Zend\Stdlib\Hydrator\NamingStrategy\NamingStrategyInterface');
        $strategy2 = m::mock('Zend\Stdlib\Hydrator\NamingStrategy\NamingStrategyInterface');

        $strategy1->shouldReceive('hydrate')->with('first', null)->andReturn('second');
        $strategy2->shouldReceive('hydrate')->with('second', null)->andReturn('third');
        $strategy1->shouldReceive('extract')->with('first_E', null)->andReturn('second_E');
        $strategy2->shouldReceive('extract')->with('second_E', null)->andReturn('third_E');

        $this->assertTrue($this->namingStrategy->addNamingStrategy($strategy2, 2), 'Failed to add the second strategy');
        $this->assertTrue($this->namingStrategy->addNamingStrategy($strategy1, 1), 'Failed to add the first strategy');

        $this->assertEquals('third', $this->namingStrategy->hydrate('first'));
        $this->assertEquals('third_E', $this->namingStrategy->extract('first_E'));
    }
}
