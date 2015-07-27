<?php

namespace Graze\Dal\Test\Unit\Adapter\EloquentOrm;

use Graze\Dal\Adapter\EloquentOrm\Configuration;
use Graze\Dal\Test\Entity;
use Graze\Dal\Test\Record;
use Illuminate\Support\Facades\Config;
use Mockery;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeConstructed()
    {
        $config = new Configuration([]);
        static::assertInstanceOf('Graze\Dal\Adapter\EloquentOrm\Configuration', $config);
    }

    public function testCanBuildMapper()
    {
        $unitOfWork = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\UnitOfWork');

        $config = new Configuration(['foo' => ['record' => 'Foo']]);
        $mapper = $config->buildMapper('foo', $unitOfWork);
        static::assertInstanceOf('Graze\Dal\Adapter\ActiveRecord\Mapper\MapperInterface', $mapper);
    }

    public function testThrowsExceptionWhenBuildingMapperWithNoRecord()
    {
        $this->setExpectedException('Graze\Dal\Exception\InvalidMappingException');
        $unitOfWork = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\UnitOfWork');

        $config = new Configuration([]);
        $mapper = $config->buildMapper('foo', $unitOfWork);
        static::assertInstanceOf('Graze\Dal\Adapter\ActiveRecord\Mapper\MapperInterface', $mapper);
    }

    public function testCanGetEntityName()
    {
        $config = new Configuration([]);
        static::assertEquals('Graze\Dal\Test\Entity', $config->getEntityName(new Entity(1)));
    }

    public function testCanBuildPersister()
    {
        $unitOfWork = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\UnitOfWork');

        $config = new Configuration(['foo' => ['record' => 'Foo']]);
        $persister = $config->buildPersister('foo', $unitOfWork);
        static::assertInstanceOf('Graze\Dal\Adapter\ActiveRecord\Persister\PersisterInterface', $persister);
    }

    public function testThrowsExceptionWhenBuildingPersisterWithNoRecord()
    {
        $this->setExpectedException('Graze\Dal\Exception\InvalidMappingException');
        $unitOfWork = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\UnitOfWork');

        $config = new Configuration([]);
        $persister = $config->buildPersister('foo', $unitOfWork);
        static::assertInstanceOf('Graze\Dal\Adapter\ActiveRecord\Persister\PersisterInterface', $persister);
    }

    public function testCanBuildRepositoryWithoutRepositoryConfig()
    {
        $adapter = Mockery::mock('Graze\Dal\Adapter\ActiveRecordAdapter');

        $config = new Configuration([]);

        $repository = $config->buildRepository('foo', $adapter);
        static::assertInstanceOf('Graze\Dal\Adapter\ActiveRecord\EntityRepository', $repository);
    }

    public function testCanBuildRepositoryWithRepositoryConfig()
    {
        $adapter = Mockery::mock('Graze\Dal\Adapter\ActiveRecordAdapter');

        $config = new Configuration(['foo' => [
            'record' => 'Foo',
            'repository' => 'Graze\Dal\Test\Repository'
        ]]);

        $repository = $config->buildRepository('foo', $adapter);
        static::assertInstanceOf('Graze\Dal\Adapter\ActiveRecord\EntityRepository', $repository);
    }

    public function testThrowsExceptionWhenBuildingInvalidRepository()
    {
        $this->setExpectedException('Graze\Dal\Exception\InvalidRepositoryException');
        $adapter = Mockery::mock('Graze\Dal\Adapter\ActiveRecordAdapter');

        $config = new Configuration(['foo' => [
            'record' => 'Foo',
            'repository' => 'Graze\Dal\Test\Entity'
        ]]);

        $repository = $config->buildRepository('foo', $adapter);
        static::assertInstanceOf('Graze\Dal\Adapter\ActiveRecord\EntityRepository', $repository);
    }

    public function testCanBuildUnitOfWork()
    {
        $adapter = Mockery::mock('Graze\Dal\Adapter\ActiveRecordAdapter');

        $config = new Configuration([]);

        $unitOfWork = $config->buildUnitOfWork($adapter);
        static::assertInstanceOf('Graze\Dal\Adapter\ActiveRecord\UnitOfWork', $unitOfWork);
    }

    public function testCanGetEntityNameFromRecord()
    {
        $record = new Record();

        $config = new Configuration([
            'Graze\Dal\Test\Entity' => [
                'record' => 'Graze\Dal\Test\Record'
            ]
        ]);

        static::assertEquals('Graze\Dal\Test\Entity', $config->getEntityNameFromRecord($record));
    }

    public function testReturnsNullWhenEntityNameIsNotFound()
    {
        $record = new Record();

        $config = new Configuration([]);

        static::assertNull($config->getEntityNameFromRecord($record));
    }

    public function testCanBuildIdentityGenerator()
    {
        $config = new Configuration([]);
        static::assertInstanceOf(
            'Graze\Dal\Adapter\ActiveRecord\Identity\GeneratorInterface',
            $config->getIdentityGenerator()
        );
    }

    public function testCanBuildRecordNamingStrategy()
    {
        $config = new Configuration([]);
        static::assertInstanceOf(
            'Graze\Dal\NamingStrategy\NamingStrategyInterface',
            $config->buildRecordNamingStrategy('foo')
        );
    }

    public function testCanBuildEntityNamingStrategy()
    {
        $config = new Configuration([]);
        static::assertInstanceOf(
            'Graze\Dal\NamingStrategy\NamingStrategyInterface',
            $config->buildEntityNamingStrategy('foo')
        );
    }
}
