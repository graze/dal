<?php

namespace Graze\Dal\Test\Unit\Identity;

use Graze\Dal\Identity\ObjectHashGenerator;

class ObjectHashGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testCanGenerate()
    {
        $generator = new ObjectHashGenerator();
        $hash = $generator->generate(new \stdClass());
        static::assertInternalType('string', $hash);
    }
}
