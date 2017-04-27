<?php

namespace Graze\Dal\Test\Unit\Exception;

use Graze\Dal\Exception\NotSupportedException;
use Graze\Dal\Test\MockTrait;

class NotSupportedExceptionTest extends \PHPUnit_Framework_TestCase
{
    use MockTrait;

    public function testCanBeConstructedWithMessageAndAdapter()
    {
        $exception = new NotSupportedException('Foo is not supported', $this->getMockAdapter());
        static::assertInstanceOf(NotSupportedException::class, $exception);
    }
}
