<?php
namespace Graze\Dal\Adapter;

use Doctrine\ORM\EntityManagerInterface;
use Graze\Dal\Test\Entity;
use Graze\Dal\Test\UnitTestCase;
use Mockery;
use Mockery\Mock;
use PDO;

class DoctrineOrmAdapterTest extends UnitTestCase
{
    /**
     * @var DoctrineOrmAdapter
     */
    protected $adapter;

    /**
     * @var EntityManagerInterface|Mock
     */
    protected $em;

    public function setUp()
    {
        $this->em = Mockery::mock('Doctrine\ORM\EntityManagerInterface');
        $this->adapter = new DoctrineOrmAdapter($this->em);
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Graze\Dal\Adapter\AdapterInterface', $this->adapter);
    }

    public function testCanGetEntityNameFromStdClass()
    {
        $entity = new \stdClass();
        $name = $this->adapter->getEntityName($entity);
        static::assertEquals('stdClass', $name);
    }

    public function testCanGetEntityNameFromEntity()
    {
        $entity = new Entity(1);
        $name = $this->adapter->getEntityName($entity);
        static::assertEquals('Graze\Dal\Test\Entity', $name);
    }

    public function testGetRepository()
    {
        $repo = Mockery::mock('Doctrine\Common\Persistence\ObjectRepository');
        $this->em->shouldReceive('getRepository')->once()->with('foo')->andReturn($repo);

        $this->assertSame($repo, $this->adapter->getRepository('foo'));
    }

    public function testGetRepositoryThrows()
    {
        $this->em->shouldReceive('getRepository')->once()->with('foo')->andThrow('Doctrine\ORM\Mapping\MappingException');
        $this->setExpectedException('Graze\Dal\Exception\UndefinedRepositoryException');

        $this->adapter->getRepository('foo');
    }

    public function testHasRepository()
    {
        $repo = Mockery::mock('Doctrine\Common\Persistence\ObjectRepository');
        $this->em->shouldReceive('getRepository')->once()->with('foo')->andReturn($repo);

        $this->assertTrue($this->adapter->hasRepository('foo'));
    }

    public function testHasRepositoryIsFalse()
    {
        $this->em->shouldReceive('getRepository')->once()->with('foo')->andThrow('Doctrine\ORM\Mapping\MappingException');

        $this->assertFalse($this->adapter->hasRepository('foo'));
    }

    public function testFlush()
    {
        $this->em->shouldReceive('flush')->once()->withNoArgs();

        $this->assertNull($this->adapter->flush());
    }

    public function testFlushWithEntity()
    {
        $entity = new \stdClass();
        $this->em->shouldReceive('flush')->once()->with($entity);

        $this->assertNull($this->adapter->flush($entity));
    }

    public function testPersist()
    {
        $entity = new \stdClass();
        $this->em->shouldReceive('persist')->once()->with($entity);

        $this->assertNull($this->adapter->persist($entity));
    }

    public function testRefresh()
    {
        $entity = new \stdClass();
        $this->em->shouldReceive('refresh')->once()->with($entity);

        $this->assertNull($this->adapter->refresh($entity));
    }

    public function testCanRemoveEntity()
    {
        $entity = new Entity(1);
        $this->em->shouldReceive('remove')->once()->with($entity);

        $this->assertNull($this->adapter->remove($entity));
    }

    public function testBeginTransaction()
    {
        $this->em->shouldReceive('getConnection->beginTransaction')->once()->withNoArgs();

        $this->assertNull($this->adapter->beginTransaction());
    }

    public function testCommit()
    {
        $this->em->shouldReceive('getConnection->commit')->once()->withNoArgs();

        $this->assertNull($this->adapter->commit());
    }

    public function testRollback()
    {
        $this->em->shouldReceive('getConnection->rollback')->once()->withNoArgs();

        $this->assertNull($this->adapter->rollback());
    }

    public function testCanTransactClosure()
    {
        $entity = new Entity(1);
        $callable = function (DoctrineOrmAdapter $adapter) use ($entity) {
            $adapter->persist($entity);
        };

        $this->em->shouldReceive('getConnection->beginTransaction')->once()->withNoArgs();
        $this->em->shouldReceive('persist')->once()->with($entity);
        $this->em->shouldReceive('getConnection->commit')->once();

        $this->adapter->transaction($callable);
    }

    public function testCanTransactMethod()
    {
        $mock = Mockery::mock('stdClass');
        $mock->shouldReceive('transaction')
            ->andReturnUsing(function (DoctrineOrmAdapter $adapter) {
                $entity = new Entity(1);
                $adapter->persist($entity);
            });

        $this->em->shouldReceive('getConnection->beginTransaction')->once()->withNoArgs();
        $this->em->shouldReceive('persist')->once();
        $this->em->shouldReceive('getConnection->commit')->once();

        $this->adapter->transaction([$mock, 'transaction']);
    }

    public function testRollbackWhenClosureThrowsException()
    {
        $this->setExpectedException('Exception');
        $callable = function (DoctrineOrmAdapter $adapter) {
            throw new \Exception();
        };

        $this->em->shouldReceive('getConnection->beginTransaction')->once()->withNoArgs();
        $this->em->shouldReceive('getConnection->rollback')->once();

        $this->adapter->transaction($callable);
    }

    public function testCanExecuteSqlQuery()
    {
        $sql = "SELECT * FROM foo";
        $stmt = Mockery::mock('\Doctrine\DBAL\Driver\Statement');
        $stmt->shouldReceive('execute')->once()
            ->with([]);
        $stmt->shouldReceive('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->andReturn([]);

        $this->em->shouldReceive('getConnection->prepare')->once()->with($sql)
            ->andReturn($stmt);

        $result = $this->adapter->fetch($sql);
        static::assertInternalType('array', $result);
    }

    public function testCanExecuteSqlQueryAndReturnOneResult()
    {
        $sql = "SELECT * FROM foo";
        $stmt = Mockery::mock('\Doctrine\DBAL\Driver\Statement');
        $stmt->shouldReceive('execute')->once()
            ->with([]);
        $stmt->shouldReceive('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->andReturn([]);

        $this->em->shouldReceive('getConnection->prepare')->once()->with($sql)
            ->andReturn($stmt);

        $result = $this->adapter->fetchOne($sql);
        static::assertInternalType('array', $result);
    }
}
