<?php

namespace Graze\Dal\Test\Unit\Adapter\ActiveRecord\Identity;

use Graze\Dal\Adapter\ActiveRecord\Identity\ObjectHashGenerator;
use Mockery;

class ObjectHashGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeConstructed()
    {
        $generator = new ObjectHashGenerator();
        static::assertInstanceOf('Graze\Dal\Adapter\ActiveRecord\Identity\GeneratorInterface', $generator);
    }

    public function testCanGenerateForEntity()
    {
        $entity = Mockery::mock('Graze\Dal\Test\Entity');
        $generator = new ObjectHashGenerator();
        static::assertInternalType('string', $generator->generate($entity));
    }
}
