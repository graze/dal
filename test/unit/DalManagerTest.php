<?php
namespace Graze\Dal;

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
}
