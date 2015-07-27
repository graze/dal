<?php

namespace Graze\Dal\Test\Unit\Adapter\ActiveRecord\Hydrator;

use Graze\Dal\Adapter\ActiveRecord\Hydrator\AttributeHydrator;
use Graze\Dal\Test\Record;
use Mockery;

class AttributeHydratorTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeConstructed()
    {
        $hydrator = new AttributeHydrator();
        static::assertInstanceOf('Zend\StdLib\Hydrator\HydratorInterface', $hydrator);
    }

    public function testCanExtractFromValidObject()
    {
        $record = Mockery::mock('Graze\Dal\Test\Record');
        $record->shouldReceive('toArray')
            ->andReturn(['foo' => 'bar']);

        $hydrator = new AttributeHydrator();
        $data = $hydrator->extract($record);

        static::assertInternalType('array', $data);
        static::assertArrayHasKey('foo', $data);
        static::assertEquals('bar', $data['foo']);
    }

    public function testThrowsExceptionExtractFromInvalidObject()
    {
        $this->setExpectedException('Graze\Dal\Exception\InvalidEntityException');

        $record = new Record();

        $hydrator = new AttributeHydrator('convertToArray');
        $hydrator->extract($record);
    }

    public function testCanExtractFromValidObjectWithFilter()
    {
        $record = Mockery::mock('Graze\Dal\Test\Record');
        $record->shouldReceive('toArray')
            ->andReturn(['foo' => 'bar', 'bat' => 'baz']);

        $hydrator = new AttributeHydrator();
        $hydrator->addFilter('filter', function ($property) {
            return $property === 'foo';
        });
        $data = $hydrator->extract($record);

        static::assertInternalType('array', $data);
        static::assertArrayHasKey('foo', $data);
        static::assertEquals('bar', $data['foo']);
        static::assertArrayNotHasKey('bat', $data);
    }

    public function testCanExtractFromValidObjectWithNamingStrategy()
    {
        $record = Mockery::mock('Graze\Dal\Test\Record');
        $record->shouldReceive('toArray')
            ->andReturn(['foo' => 'bar']);

        $namingStrategy = Mockery::mock('Zend\Stdlib\Hydrator\NamingStrategy\NamingStrategyInterface');
        $namingStrategy->shouldReceive('extract')
            ->with('foo', $record)
            ->andReturn('prefix_foo');

        $hydrator = new AttributeHydrator();
        $hydrator->setNamingStrategy($namingStrategy);
        $data = $hydrator->extract($record);

        static::assertInternalType('array', $data);
        static::assertArrayHasKey('prefix_foo', $data);
        static::assertEquals('bar', $data['prefix_foo']);
    }

    public function testCanHydrateValidObject()
    {
        $record = Mockery::mock('Graze\Dal\Test\Record');
        $record->shouldReceive('fromArray')
            ->with(['foo' => 'bar'])
            ->andReturnUsing(function ($data) use ($record) {
                $record->foo = $data['foo'];
            });

        $hydrator = new AttributeHydrator();

        $object = $hydrator->hydrate(['foo' => 'bar'], $record);

        static::assertObjectHasAttribute('foo', $object);
    }

    public function testThrowsExceptionHydratingInvalidObject()
    {
        $this->setExpectedException('Graze\Dal\Exception\InvalidEntityException');

        $hydrator = new AttributeHydrator();
        $hydrator->hydrate(['bat' => 'baz'], new \stdClass());
    }
}
