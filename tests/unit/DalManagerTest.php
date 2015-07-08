<?php

namespace Graze\Dal\Test\Unit;

use Exception;
use Graze\Dal\DalManager;
use Graze\Dal\Test\UnitTestCase;
use Mockery;

class DalManagerTest extends UnitTestCase
{
    public function setUp()
    {
        $this->adapterA = $a = Mockery::mock('Graze\Dal\Adapter\AdapterInterface');
        $this->adapterB = $b = Mockery::mock('Graze\Dal\Adapter\AdapterInterface');
        $this->adapterC = $c = Mockery::mock('Graze\Dal\Adapter\AdapterInterface');
        $this->adapters = ['foo'=>$a, 'bar'=>$b, 'baz'=>$c];
    }

    public function dataGetRepository()
    {
        return [
            [true, false, false],
            [false, true, false],
            [false, false, true],
            [false, false, false]
        ];
    }

    public function dataFlushWithEntity()
    {
        return $this->dataGetRepository();
    }

    public function dataPersist()
    {
        return $this->dataGetRepository();
    }

    public function dataRefresh()
    {
        return $this->dataGetRepository();
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Graze\Dal\DalManagerInterface', new DalManager());
    }

    public function testGet()
    {
        $manager = new DalManager($this->adapters);

        $this->assertSame($this->adapterB, $manager->get('bar'));
    }

    public function testGetThrows()
    {
        $manager = new DalManager();

        $this->setExpectedException('Graze\Dal\Exception\UndefinedAdapterException');

        $manager->get('bar');
    }

    public function testHas()
    {
        $manager = new DalManager($this->adapters);

        $this->assertTrue($manager->has('bar'));
    }

    public function testHasThrows()
    {
        $manager = new DalManager();

        $this->assertFalse($manager->has('bar'));
    }

    public function testSet()
    {
        $manager = new DalManager($this->adapters);

        $adapter = Mockery::mock('Graze\Dal\Adapter\AdapterInterface');
        $manager->set('foo', $adapter);

        $this->assertSame($adapter, $manager->get('foo'));
    }

    /**
     * @dataProvider dataGetRepository
     */
    public function testGetRepository($hasA, $hasB, $hasC)
    {
        $name = 'repoName';
        $repo = Mockery::mock('Doctrine\Common\Persistence\ObjectRepository');
        $manager = new DalManager($this->adapters);

        $this->adapterA->shouldReceive('hasRepository')->once()->with($name)->andReturn($hasA);
        if ($hasA) $this->adapterA->shouldReceive('getRepository')->once()->with($name)->andReturn($repo);

        if (!$hasA) $this->adapterB->shouldReceive('hasRepository')->once()->with($name)->andReturn($hasB);
        if (!$hasA && $hasB) $this->adapterB->shouldReceive('getRepository')->once()->with($name)->andReturn($repo);

        if (!$hasA && !$hasB) $this->adapterC->shouldReceive('hasRepository')->once()->with($name)->andReturn($hasC);
        if (!$hasA && !$hasB && $hasC) $this->adapterC->shouldReceive('getRepository')->once()->with($name)->andReturn($repo);

        if (!$hasA && !$hasB && !$hasC) {
            $this->setExpectedException('Graze\Dal\Exception\UndefinedRepositoryException');
            $manager->getRepository($name);
        } else {
            $this->assertSame($repo, $manager->getRepository($name));
        }
    }

    public function testFlush()
    {
        $manager = new DalManager($this->adapters);

        $this->adapterA->shouldReceive('flush')->once()->withNoArgs();
        $this->adapterB->shouldReceive('flush')->once()->withNoArgs();
        $this->adapterC->shouldReceive('flush')->once()->withNoArgs();

        $this->assertNull($manager->flush());
    }

    /**
     * @dataProvider dataFlushWithEntity
     */
    public function testFlushWithEntity($hasA, $hasB, $hasC)
    {
        $name = 'entityName';
        $entity = new \stdClass();
        $manager = new DalManager($this->adapters);

        $this->adapterA->shouldReceive('getEntityName')->once()->with($entity)->andReturn($name);
        $this->adapterA->shouldReceive('hasRepository')->once()->with($name)->andReturn($hasA);
        if ($hasA) $this->adapterA->shouldReceive('flush')->once()->with($entity);

        if (!$hasA) $this->adapterB->shouldReceive('getEntityName')->once()->with($entity)->andReturn($name);
        if (!$hasA) $this->adapterB->shouldReceive('hasRepository')->once()->with($name)->andReturn($hasB);
        if (!$hasA && $hasB) $this->adapterB->shouldReceive('flush')->once()->with($entity);

        if (!$hasA && !$hasB) $this->adapterC->shouldReceive('getEntityName')->once()->with($entity)->andReturn($name);
        if (!$hasA && !$hasB) $this->adapterC->shouldReceive('hasRepository')->once()->with($name)->andReturn($hasC);
        if (!$hasA && !$hasB && $hasC) $this->adapterC->shouldReceive('flush')->once()->with($entity);

        if (!$hasA && !$hasB && !$hasC) {
            $this->setExpectedException('Graze\Dal\Exception\UndefinedAdapterException');
            $manager->flush($entity);
        } else {
            $this->assertNull($manager->flush($entity));
        }
    }

    /**
     * @dataProvider dataPersist
     */
    public function testPersist($hasA, $hasB, $hasC)
    {
        $name = 'entityName';
        $entity = new \stdClass();
        $manager = new DalManager($this->adapters);

        $this->adapterA->shouldReceive('getEntityName')->once()->with($entity)->andReturn($name);
        $this->adapterA->shouldReceive('hasRepository')->once()->with($name)->andReturn($hasA);
        if ($hasA) $this->adapterA->shouldReceive('persist')->once()->with($entity);

        if (!$hasA) $this->adapterB->shouldReceive('getEntityName')->once()->with($entity)->andReturn($name);
        if (!$hasA) $this->adapterB->shouldReceive('hasRepository')->once()->with($name)->andReturn($hasB);
        if (!$hasA && $hasB) $this->adapterB->shouldReceive('persist')->once()->with($entity);

        if (!$hasA && !$hasB) $this->adapterC->shouldReceive('getEntityName')->once()->with($entity)->andReturn($name);
        if (!$hasA && !$hasB) $this->adapterC->shouldReceive('hasRepository')->once()->with($name)->andReturn($hasC);
        if (!$hasA && !$hasB && $hasC) $this->adapterC->shouldReceive('persist')->once()->with($entity);

        if (!$hasA && !$hasB && !$hasC) {
            $this->setExpectedException('Graze\Dal\Exception\UndefinedAdapterException');
            $manager->persist($entity);
        } else {
            $this->assertNull($manager->persist($entity));
        }
    }

    /**
     * @dataProvider dataRefresh
     */
    public function testRefresh($hasA, $hasB, $hasC)
    {
        $name = 'entityName';
        $entity = new \stdClass();
        $manager = new DalManager($this->adapters);

        $this->adapterA->shouldReceive('getEntityName')->once()->with($entity)->andReturn($name);
        $this->adapterA->shouldReceive('hasRepository')->once()->with($name)->andReturn($hasA);
        if ($hasA) $this->adapterA->shouldReceive('refresh')->once()->with($entity);

        if (!$hasA) $this->adapterB->shouldReceive('getEntityName')->once()->with($entity)->andReturn($name);
        if (!$hasA) $this->adapterB->shouldReceive('hasRepository')->once()->with($name)->andReturn($hasB);
        if (!$hasA && $hasB) $this->adapterB->shouldReceive('refresh')->once()->with($entity);

        if (!$hasA && !$hasB) $this->adapterC->shouldReceive('getEntityName')->once()->with($entity)->andReturn($name);
        if (!$hasA && !$hasB) $this->adapterC->shouldReceive('hasRepository')->once()->with($name)->andReturn($hasC);
        if (!$hasA && !$hasB && $hasC) $this->adapterC->shouldReceive('refresh')->once()->with($entity);

        if (!$hasA && !$hasB && !$hasC) {
            $this->setExpectedException('Graze\Dal\Exception\UndefinedAdapterException');
            $manager->refresh($entity);
        } else {
            $this->assertNull($manager->refresh($entity));
        }
    }

    public function testTransactionCommit()
    {
        $manager = new DalManager($this->adapters);

//        $this->adapterB->shouldReceive('beginTransaction')->once()->withNoArgs();
//        $this->adapterB->shouldReceive('commit')->once()->withNoArgs();
        $this->adapterB->shouldReceive('transaction')->once();

        $manager->transaction('bar', function ($adapter) {
            $this->assertSame($this->adapterB, $adapter);
        });
    }

    public function testTransactionRollback()
    {
        $manager = new DalManager($this->adapters);
        $exception = new Exception('Transaction failed');

//        $this->adapterB->shouldReceive('beginTransaction')->once()->withNoArgs();
//        $this->adapterB->shouldReceive('rollback')->once()->withNoArgs();
        $this->adapterB->shouldReceive('transaction')->once();

        try {
            $manager->transaction('bar', function ($adapter) use ($exception) {
                $this->assertSame($this->adapterB, $adapter);

                throw $exception;
            });
        } catch (Exception $e) {
            $this->assertSame($exception, $e);
        }
    }
}
