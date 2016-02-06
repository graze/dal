<?php

namespace Graze\Dal\Test\Unit\Entity;

use Graze\Dal\Entity\EntityMetadata;
use Graze\Dal\Test\MockTrait;

class EntityMetadataTest extends \PHPUnit_Framework_TestCase
{
    use MockTrait;

    public function testCanBeConstructed()
    {
        $entity = $this->getMockEntity();
        $config = $this->getMockConfig();

        $metadata = new EntityMetadata($entity, $config);
        static::assertInstanceOf('Graze\Dal\Entity\EntityMetadata', $metadata);
    }

    public function testCanGetRelationshipMetadata()
    {
        $entity = $this->getMockEntity();
        $config = $this->getMockConfig();
        $config->shouldReceive('getMapping')
            ->with(get_class($entity))
            ->andReturn([
                'related' => ['foo' => 'bar']
            ]);

        $metadata = new EntityMetadata($entity, $config);
        $relationships = $metadata->getRelationshipMetadata();

        static::assertNotEmpty($relationships);
        static::assertEquals($relationships, ['foo' => 'bar']);
    }

    public function testGetRelationshipMetadataReturnsEmptyArray()
    {
        $entity = $this->getMockEntity();
        $config = $this->getMockConfig();
        $config->shouldReceive('getMapping')
            ->with(get_class($entity))
            ->andReturn([]);

        $metadata = new EntityMetadata($entity, $config);
        $relationships = $metadata->getRelationshipMetadata();

        static::assertEmpty($relationships);
    }

    public function testCanHasRelationship()
    {
        $entity = $this->getMockEntity();
        $config = $this->getMockConfig();
        $config->shouldReceive('getMapping')
            ->with(get_class($entity))
            ->andReturn([
                'related' => ['foo' => 'bar']
            ]);

        $metadata = new EntityMetadata($entity, $config);
        static::assertTrue($metadata->hasRelationship('foo'));
        static::assertFalse($metadata->hasRelationship('bar'));
    }
}
