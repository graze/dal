<?php

namespace Graze\Dal\Test\Unit\Adapter;

use Graze\Dal\Adapter\EloquentOrmAdapter;
use Graze\Dal\Test\Entity;
use Mockery;
use Mockery\Mock;

class EloquentOrmAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EloquentOrmAdapter
     */
    protected $adapter;

    /**
     * @var \Illuminate\Database\ConnectionInterface|Mock
     */
    protected $connection;

    /**
     * @var \Graze\Dal\Adapter\ActiveRecord\UnitOfWork|Mock
     */
    protected $unitOfWork;

    /**
     * @var \Graze\Dal\Adapter\EloquentOrm\Configuration|Mock
     */
    protected $config;

    public function setUp()
    {
        $this->connection = Mockery::mock('Illuminate\Database\ConnectionInterface');
        $this->unitOfWork = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\UnitOfWork');
        $this->config = Mockery::mock('Graze\Dal\Adapter\EloquentOrm\Configuration');
        $this->config->shouldReceive('buildUnitOfWork')
            ->andReturn($this->unitOfWork);

        $this->adapter = new EloquentOrmAdapter($this->connection, $this->config);
    }

    public function testCanBeConstructed()
    {
        static::assertInstanceOf('Graze\Dal\Adapter\EloquentOrmAdapter', $this->adapter);
    }

    public function testCanGetEntityName()
    {
        $entity = new Entity(1);
        $this->config->shouldReceive('getEntityName')
            ->once()
            ->andReturn(get_class($entity));

        static::assertEquals('Graze\Dal\Test\Entity', $this->adapter->getEntityName($entity));
    }

    public function testCanGetRepositoryForValidName()
    {
        $repository = Mockery::mock('Doctrine\Common\Persistence\ObjectRepository');

        $this->config->shouldReceive('getMapping')
            ->with('foo')
            ->andReturn(['repository' => $repository]);
        $this->config->shouldReceive('buildRepository')
            ->with('foo', $this->adapter)
            ->andReturn($repository);

        static::assertEquals($repository, $this->adapter->getRepository('foo'));
    }

    public function testExceptionThrownForUndefinedRepository()
    {
        $this->setExpectedException('Graze\Dal\Exception\UndefinedRepositoryException');

        $this->config->shouldReceive('getMapping')
            ->with('foo')
            ->andReturnNull();

        $this->adapter->getRepository('foo');
    }

    public function testCanGetUnitOfWork()
    {
        static::assertEquals($this->adapter->getUnitOfWork(), $this->unitOfWork);
    }

    public function testHasRepositoryReturnsTrueForValidName()
    {
        $this->config->shouldReceive('getMapping')
            ->with('foo')
            ->andReturn(['repository' => Mockery::mock('Doctrine\Common\Persistence\ObjectRepository')]);

        static::assertTrue($this->adapter->hasRepository('foo'));
    }

    public function testHasRepositoryReturnsFalseForInvalidName()
    {
        $this->config->shouldReceive('getMapping')
            ->with('foo')
            ->andReturn(false);

        static::assertFalse($this->adapter->hasRepository('foo'));
    }

    public function testCanFlushForAllEntities()
    {
        $this->unitOfWork->shouldReceive('commit')->once()->withNoArgs();
        static::assertNull($this->adapter->flush());
    }

    public function testCanFlushForSingleEntity()
    {
        $entity = Mockery::mock('Graze\Dal\Test\Entity');
        $this->unitOfWork->shouldReceive('commit')->once()->with($entity);
        static::assertNull($this->adapter->flush($entity));
    }

    public function testCanPersistEntity()
    {
        $entity = Mockery::mock('Graze\Dal\Test\Entity');
        $this->unitOfWork->shouldReceive('persist')->once()->with($entity);
        static::assertNull($this->adapter->persist($entity));
    }

    public function testCanRefreshEntity()
    {
        $entity = Mockery::mock('Graze\Dal\Test\Entity');
        $this->unitOfWork->shouldReceive('refresh')->once()->with($entity);
        static::assertNull($this->adapter->refresh($entity));
    }

    public function testCanRemoveEntity()
    {
        $entity = Mockery::mock('Graze\Dal\Test\Entity');
        $this->unitOfWork->shouldReceive('remove')->once()->with($entity);
        static::assertNull($this->adapter->remove($entity));
    }

    public function testCanTransactClosure()
    {
        $entity = new Entity(1);
        $callable = function (EloquentOrmAdapter $adapter) use ($entity) {
            $adapter->persist($entity);
        };

        $this->connection->shouldReceive('beginTransaction')->once()->withNoArgs();
        $this->unitOfWork->shouldReceive('persist')->once()->with($entity);
        $this->connection->shouldReceive('commit')->once();

        $this->adapter->transaction($callable);
    }

    public function testCanTransactMethod()
    {
        $mock = Mockery::mock('stdClass');
        $mock->shouldReceive('transaction')
            ->andReturnUsing(function (EloquentOrmAdapter $adapter) {
                $entity = new Entity(1);
                $adapter->persist($entity);
            });

        $this->connection->shouldReceive('beginTransaction')->once()->withNoArgs();
        $this->unitOfWork->shouldReceive('persist')->once();
        $this->connection->shouldReceive('commit')->once();

        $this->adapter->transaction([$mock, 'transaction']);
    }

    public function testRollbackWhenClosureThrowsException()
    {
        $this->setExpectedException('Exception');
        $callable = function (EloquentOrmAdapter $adapter) {
            throw new \Exception();
        };

        $this->connection->shouldReceive('beginTransaction')->once()->withNoArgs();
        $this->connection->shouldReceive('rollBack')->once();

        $this->adapter->transaction($callable);
    }

    public function testCanBeginTransaction()
    {
        $this->connection->shouldReceive('beginTransaction')->once()->withNoArgs();
        static::assertNull($this->adapter->beginTransaction());
    }

    public function testCanCommit()
    {
        $this->connection->shouldReceive('commit')->once()->withNoArgs();
        static::assertNull($this->adapter->commit());
    }

    public function testCanRollback()
    {
        $this->connection->shouldReceive('rollBack')->once()->withNoArgs();
        static::assertNull($this->adapter->rollback());
    }

    public function testCanExecuteSqlQuery()
    {
        $sql = "SELECT * FROM foo";

        $this->connection->shouldReceive('select')->once()->with($sql, [])
            ->andReturn([]);

        $result = $this->adapter->fetch($sql);
        static::assertInternalType('array', $result);
    }

    public function testCanExecuteSqlQueryAndReturnOneResult()
    {
        $sql = "SELECT * FROM foo";

        $this->connection->shouldReceive('selectOne')->once()->with($sql, [])
            ->andReturn([]);

        $result = $this->adapter->fetchOne($sql);
        static::assertInternalType('array', $result);
    }
}
