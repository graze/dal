<?php

namespace Graze\Dal\Test\Unit\Adapter\ActiveRecord\Hydrator;

use Graze\Dal\Adapter\ActiveRecord\Hydrator\MethodProxyHydrator;
use Mockery;

class MethodProxyHydratorTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeConstructed()
    {
        $config = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface');
        $proxyFactory = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\Proxy\ProxyFactory');

        $hydrator = new MethodProxyHydrator($config, $proxyFactory);
        static::assertInstanceOf('Graze\Dal\Adapter\ActiveRecord\Hydrator\MethodProxyHydrator', $hydrator);
    }

    public function testCanBeConstructedWithNext()
    {
        $config = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface');
        $proxyFactory = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\Proxy\ProxyFactory');
        $nexyHydrator = Mockery::mock('Zend\Stdlib\Hydrator\HydratorInterface');

        $hydrator = new MethodProxyHydrator($config, $proxyFactory, $nexyHydrator);
        static::assertInstanceOf('Graze\Dal\Adapter\ActiveRecord\Hydrator\MethodProxyHydrator', $hydrator);
    }

    public function testCanExtractFromValidObject()
    {
        $record = Mockery::mock('Graze\Dal\Test\Record');

        $config = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface');
        $config->shouldReceive('getEntityNameFromRecord')
            ->with($record)
            ->andReturn('Graze\Dal\Test\Entity');
        $config->shouldReceive('getMapping')
            ->with('Graze\Dal\Test\Entity')
            ->andReturn([
                'record' => 'Graze\Dal\Test\Record',
                'related' => [
                    'foo' => [
                        'entity' => 'Graze\Dal\Test\Entity',
                        'method' => 'getFoo',
                    ]
                ],
            ]);

        $proxyFactory = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\Proxy\ProxyFactory');
        $proxyFactory->shouldReceive('buildEntityProxy')
            ->with('Graze\Dal\Test\Entity', [$record, 'getFoo'], [])
            ->andReturn(Mockery::mock('ProxyManager\Proxy\GhostObjectInterface'));

        $hydrator = new MethodProxyHydrator($config, $proxyFactory);
        $data = $hydrator->extract($record);

        static::assertInternalType('array', $data);
        static::assertArrayHasKey('foo', $data);
        static::assertInstanceOf('ProxyManager\Proxy\GhostObjectInterface', $data['foo']);
    }

    public function testCanExtractFromValidObjectWithNext()
    {
        $record = Mockery::mock('Graze\Dal\Test\Record');

        $config = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface');
        $config->shouldReceive('getEntityNameFromRecord')
            ->with($record)
            ->andReturn('Graze\Dal\Test\Entity');
        $config->shouldReceive('getMapping')
            ->with('Graze\Dal\Test\Entity')
            ->andReturn([
                'record' => 'Graze\Dal\Test\Record',
                'related' => [
                    'foo' => [
                        'entity' => 'Graze\Dal\Test\Entity',
                        'method' => 'getFoo',
                    ]
                ],
            ]);

        $proxyFactory = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\Proxy\ProxyFactory');
        $proxyFactory->shouldReceive('buildEntityProxy')
            ->with('Graze\Dal\Test\Entity', [$record, 'getFoo'], [])
            ->andReturn(Mockery::mock('ProxyManager\Proxy\GhostObjectInterface'));

        $nextHydrator = Mockery::mock('Zend\Stdlib\Hydrator\HydratorInterface');
        $nextHydrator->shouldReceive('extract')
            ->with($record)
            ->once()
            ->andReturn([]);

        $hydrator = new MethodProxyHydrator($config, $proxyFactory, $nextHydrator);
        $data = $hydrator->extract($record);

        static::assertInternalType('array', $data);
        static::assertArrayHasKey('foo', $data);
        static::assertInstanceOf('ProxyManager\Proxy\GhostObjectInterface', $data['foo']);
    }

    public function testCanExtractFromValidObjectWithCollectionTrue()
    {
        $record = Mockery::mock('Graze\Dal\Test\Record');

        $config = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface');
        $config->shouldReceive('getEntityNameFromRecord')
            ->with($record)
            ->andReturn('Graze\Dal\Test\Entity');
        $config->shouldReceive('getMapping')
            ->with('Graze\Dal\Test\Entity')
            ->andReturn([
                'record' => 'Graze\Dal\Test\Record',
                'related' => [
                    'foo' => [
                        'entity' => 'Graze\Dal\Test\Entity',
                        'method' => 'getFoo',
                        'collection' => true
                    ]
                ],
            ]);

        $proxyFactory = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\Proxy\ProxyFactory');
        $proxyFactory->shouldReceive('buildCollectionProxy')
            ->with('Graze\Dal\Test\Entity', [$record, 'getFoo'], null, [])
            ->andReturn(Mockery::mock('ProxyManager\Proxy\GhostObjectInterface'));

        $hydrator = new MethodProxyHydrator($config, $proxyFactory);
        $data = $hydrator->extract($record);

        static::assertInternalType('array', $data);
        static::assertArrayHasKey('foo', $data);
        static::assertInstanceOf('ProxyManager\Proxy\GhostObjectInterface', $data['foo']);
    }

    public function testCanExtractFromValidObjectWithCollectionClassName()
    {
        $record = Mockery::mock('Graze\Dal\Test\Record');

        $config = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface');
        $config->shouldReceive('getEntityNameFromRecord')
            ->with($record)
            ->andReturn('Graze\Dal\Test\Entity');
        $config->shouldReceive('getMapping')
            ->with('Graze\Dal\Test\Entity')
            ->andReturn([
                'record' => 'Graze\Dal\Test\Record',
                'related' => [
                    'foo' => [
                        'entity' => 'Graze\Dal\Test\Entity',
                        'method' => 'getFoo',
                        'collection' => 'Graze\Dal\Test\Collection'
                    ]
                ],
            ]);

        $proxyFactory = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\Proxy\ProxyFactory');
        $proxyFactory->shouldReceive('buildCollectionProxy')
            ->with('Graze\Dal\Test\Entity', [$record, 'getFoo'], 'Graze\Dal\Test\Collection', [])
            ->andReturn(Mockery::mock('ProxyManager\Proxy\GhostObjectInterface'));

        $hydrator = new MethodProxyHydrator($config, $proxyFactory);
        $data = $hydrator->extract($record);

        static::assertInternalType('array', $data);
        static::assertArrayHasKey('foo', $data);
        static::assertInstanceOf('ProxyManager\Proxy\GhostObjectInterface', $data['foo']);
    }

    public function testThrowsExceptionExtractingInvalidRelatedMapping()
    {
        $this->setExpectedException('Graze\Dal\Exception\InvalidMappingException');

        $record = Mockery::mock('Graze\Dal\Test\Record');

        $config = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface');
        $config->shouldReceive('getEntityNameFromRecord')
            ->with($record)
            ->andReturn('Graze\Dal\Test\Entity');
        $config->shouldReceive('getMapping')
            ->with('Graze\Dal\Test\Entity')
            ->andReturn([
                'record' => 'Graze\Dal\Test\Record',
                'related' => [
                    'foo' => [
                    ]
                ],
            ]);

        $proxyFactory = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\Proxy\ProxyFactory');

        $hydrator = new MethodProxyHydrator($config, $proxyFactory);
        $hydrator->extract($record);
    }

    public function testThrowsExceptionExtractingNoRelated()
    {
        $record = Mockery::mock('Graze\Dal\Test\Record');

        $config = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface');
        $config->shouldReceive('getEntityNameFromRecord')
            ->with($record)
            ->andReturn('Graze\Dal\Test\Entity');
        $config->shouldReceive('getMapping')
            ->with('Graze\Dal\Test\Entity')
            ->andReturn([
                'record' => 'Graze\Dal\Test\Record',
            ]);

        $proxyFactory = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\Proxy\ProxyFactory');

        $hydrator = new MethodProxyHydrator($config, $proxyFactory);

        static::assertEmpty($hydrator->extract($record));
    }

    public function testCanHydrate()
    {
        $config = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface');
        $proxyFactory = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\Proxy\ProxyFactory');
        $record = Mockery::mock('Graze\Dal\Test\Record');

        $hydrator = new MethodProxyHydrator($config, $proxyFactory);
        $obj = $hydrator->hydrate(['foo' => 'bar'], $record);

        static::assertEquals($obj, $record);
    }

    public function testCanHydrateWithNext()
    {
        $config = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface');
        $proxyFactory = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\Proxy\ProxyFactory');
        $record = Mockery::mock('Graze\Dal\Test\Record');

        $nextHydrator = Mockery::mock('Zend\Stdlib\Hydrator\HydratorInterface');
        $nextHydrator->shouldReceive('hydrate')
            ->with(['foo' => 'bar'], $record)
            ->once();

        $hydrator = new MethodProxyHydrator($config, $proxyFactory, $nextHydrator);
        $obj = $hydrator->hydrate(['foo' => 'bar'], $record);

        static::assertEquals($obj, $record);
    }
}
