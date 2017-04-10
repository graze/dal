<?php

namespace Graze\Dal\Test\Unit\Exception;

use Graze\Dal\Entity\EntityInterface;
use Graze\Dal\Exception\InvalidEntityException;
use Mockery;

class InvalidEntityExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeConstructedWithStringEntity()
    {
        $exception = new InvalidEntityException('FooEntity');
        static::assertInstanceOf(InvalidEntityException::class, $exception);
    }

    public function testCanBeConstructedWithClassEntity()
    {
        $entity = Mockery::mock(EntityInterface::class);
        $exception = new InvalidEntityException($entity);
        static::assertInstanceOf(InvalidEntityException::class, $exception);
    }

    public function testCanBeConstructedWithMethod()
    {
        $entity = Mockery::mock(EntityInterface::class);
        $exception = new InvalidEntityException($entity, __METHOD__);
        static::assertInstanceOf(InvalidEntityException::class, $exception);
    }

    public function testCanGetEntity()
    {
        $entity = Mockery::mock(EntityInterface::class);
        $exception = new InvalidEntityException($entity);
        static::assertSame($entity, $exception->getEntity());
    }
}
