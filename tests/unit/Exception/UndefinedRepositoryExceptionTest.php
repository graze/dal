<?php

namespace Graze\Dal\Test\Unit\Exception;

use Graze\Dal\Exception\UndefinedRepositoryException;

class UndefinedRepositoryExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeConstructedWithName()
    {
        $exception = new UndefinedRepositoryException('foo');
        static::assertInstanceOf(UndefinedRepositoryException::class, $exception);
    }

    public function testCanBeConstructedWithNameAndMethod()
    {
        $exception = new UndefinedRepositoryException('foo', __METHOD__);
        static::assertInstanceOf(UndefinedRepositoryException::class, $exception);
    }

    public function testCanGetName()
    {
        $exception = new UndefinedRepositoryException('foo');
        self::assertSame($exception->getName(), 'foo');
    }
}
