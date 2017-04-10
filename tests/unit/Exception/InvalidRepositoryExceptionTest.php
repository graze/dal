<?php

namespace Graze\Dal\Test\Unit\Exception;

use Graze\Dal\Exception\InvalidRepositoryException;
use Mockery;

class InvalidRepositoryExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeConstructedWithStringRepository()
    {
        $exception = new InvalidRepositoryException('FooEntity');
        static::assertInstanceOf(InvalidRepositoryException::class, $exception);
    }

    public function testCanBeConstructedWithClassRepository()
    {
        $repository = Mockery::mock('stdClass');
        $exception = new InvalidRepositoryException($repository);
        static::assertInstanceOf(InvalidRepositoryException::class, $exception);
    }

    public function testCanBeConstructedWithMethod()
    {
        $repository = Mockery::mock('stdClass');
        $exception = new InvalidRepositoryException($repository, __METHOD__);
        static::assertInstanceOf(InvalidRepositoryException::class, $exception);
    }

    public function testCanGetRepository()
    {
        $repository = Mockery::mock('stdClass');
        $exception = new InvalidRepositoryException($repository);
        static::assertSame($repository, $exception->getRepository());
    }
}
