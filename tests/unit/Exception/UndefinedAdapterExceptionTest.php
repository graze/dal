<?php

namespace Graze\Dal\Test\Unit\Exception;

use Graze\Dal\Exception\UndefinedAdapterException;

class UndefinedAdapterExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeConstructedWithName()
    {
        $exception = new UndefinedAdapterException('foo');
        static::assertInstanceOf(UndefinedAdapterException::class, $exception);
    }

    public function testCanBeConstructedWithNameAndMethod()
    {
        $exception = new UndefinedAdapterException('foo', __METHOD__);
        static::assertInstanceOf(UndefinedAdapterException::class, $exception);
    }

    public function testCanGetName()
    {
        $exception = new UndefinedAdapterException('foo');
        static::assertSame($exception->getName(), 'foo');
    }
}
