<?php

namespace Graze\Dal\Test\Unit\Exception;

use Graze\Dal\Exception\NotImplementedException;

class NotImplementedExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeConstructedWithClassAndMethod()
    {
        $excepton = new NotImplementedException('Foo', 'bar');
        static::assertInstanceOf(NotImplementedException::class, $excepton);
    }
}
