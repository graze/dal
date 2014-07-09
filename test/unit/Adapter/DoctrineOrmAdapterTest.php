<?php
namespace Graze\Dal\Adapter;

use Graze\Dal\Test\UnitTestCase;
use Mockery;

class DoctrineOrmAdapterTest extends UnitTestCase
{
    public function setUp()
    {
        $this->em = Mockery::mock('Doctrine\ORM\EntityManagerInterface');

        $this->adapter = new DoctrineOrmAdapter($this->em);
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Graze\Dal\Adapter\AdapterInterface', $this->adapter);
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
}
