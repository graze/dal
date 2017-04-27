<?php

namespace Graze\Dal\Test\Unit\Exception;

use Graze\Dal\Exception\MissingConfigException;

class MissingConfigExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeConstructedWithEntityNameAndConfigField()
    {
        $exception = new MissingConfigException('Foo', 'bar');
        static::assertInstanceOf(MissingConfigException::class, $exception);
    }
}
