<?php

namespace Graze\Dal\Test\Unit\Persister;

use Graze\Dal\Adapter\AdapterInterface;
use Graze\Dal\Configuration\ConfigurationInterface;
use Graze\Dal\Entity\EntityInterface;
use Graze\Dal\Mapper\MapperInterface;
use Graze\Dal\Persister\AbstractPersister;
use Graze\Dal\Relationship\ManyToManyInterface;
use Graze\Dal\Test\MockTrait;
use Graze\Dal\UnitOfWork\UnitOfWorkInterface;
use Mockery;

class AbstractPersisterTest extends \PHPUnit_Framework_TestCase
{
    use MockTrait;

    public function testCanGetEntityName()
    {
        $persister = $this->getMockAbstractPersister();
        static::assertEquals($persister->getEntityName(), 'entityName');
    }

    public function testCanGetRecordName()
    {
        $persister = $this->getMockAbstractPersister();
        static::assertEquals($persister->getRecordName(), 'recordName');
    }

    /**
     * @dataProvider recordProvider
     *
     * @param $record
     */
    public function testCanLoad($record)
    {
        $entity = $this->getMockEntity();

        $mapper = $this->getMockMapper();
        $mapper->shouldReceive('toEntity')
            ->once()
            ->andReturn($entity);

        $unitOfWork = $this->getMockUnitOfWorkWithMapper($mapper);
        $unitOfWork->shouldReceive('setEntityRecord')
            ->with($entity, $record)
            ->once();
        $unitOfWork->shouldReceive('persistByTrackingPolicy')
            ->with($entity)
            ->once();

        $persister = $this->getMockAbstractPersister($unitOfWork);
        $persister->shouldReceive('loadRecord')
            ->with(['foo' => 'bar'], null, null)
            ->andReturn($record);

        $entity = $persister->load(['foo' => 'bar']);
        static::assertNotNull($entity);
    }

    public function testLoadReturnsNull()
    {
        $persister = $this->getMockAbstractPersister();
        $persister->shouldReceive('loadRecord')
            ->andReturnNull();

        $criteria = ['foo' => 'bar'];

        $entity = $persister->load($criteria);
        static::assertNull($entity);
    }

    /**
     * @dataProvider recordProvider
     *
     * @param $record
     */
    public function testCanLoadAll($record)
    {
        $entity = $this->getMockEntity();

        $mapper = $this->getMockMapper();
        $mapper->shouldReceive('toEntity')
            ->once()
            ->andReturn($entity);

        $unitOfWork = $this->getMockUnitOfWorkWithMapper($mapper);
        $unitOfWork->shouldReceive('setEntityRecord')
            ->with($entity, $record)
            ->once();
        $unitOfWork->shouldReceive('persistByTrackingPolicy')
            ->with($entity)
            ->once();

        $persister = $this->getMockAbstractPersister($unitOfWork);
        $persister->shouldReceive('loadAllRecords')
            ->with(['foo' => 'bar'], null, null, null)
            ->andReturn([$record]);

        $entities = $persister->loadAll(['foo' => 'bar']);
        static::assertNotEmpty($entities);
    }

    public function testLoadAllReturnsEmptyArray()
    {
        $mapper = $this->getMockMapper();

        $persister = $this->getMockAbstractPersisterWithMapper($mapper);
        $persister->shouldReceive('loadAllRecords')
            ->with(['foo' => 'bar'], null, null, null)
            ->andReturn([]);

        $entities = $persister->loadAll(['foo' => 'bar']);
        static::assertEmpty($entities);
    }

    /**
     * @dataProvider recordProvider
     *
     * @param $record
     */
    public function testCanLoadById($record)
    {
        $entity = $this->getMockEntity();

        $mapper = $this->getMockMapper();
        $mapper->shouldReceive('toEntity')
            ->with($record, null)
            ->once()
            ->andReturn($entity);

        $unitOfWork = $this->getMockUnitOfWorkWithMapper($mapper);
        $unitOfWork->shouldReceive('setEntityRecord')
            ->with($entity, $record)
            ->once();
        $unitOfWork->shouldReceive('persistByTrackingPolicy')
            ->with($entity)
            ->once();

        $persister = $this->getMockAbstractPersister($unitOfWork);
        $persister->shouldReceive('loadRecordById')
            ->with(1, null)
            ->andReturn($record);

        $entity = $persister->loadById(1);
        static::assertNotNull($entity);
    }

    public function testLoadByIdReturnsNull()
    {
        $mapper = $this->getMockMapper();

        $persister = $this->getMockAbstractPersisterWithMapper($mapper);
        $persister->shouldReceive('loadRecordById')
            ->with(1, null)
            ->andReturnNull();

        $entity = $persister->loadById(1);
        static::assertNull($entity);
    }

    /**
     * @dataProvider entityProvider
     *
     * @param EntityInterface $entity
     * @param array $entityData
     * @param array|\stdClass $record
     */
    public function testCanRefresh($entity, $entityData, $record)
    {
        $mapper = $this->getMockMapper();
        $mapper->shouldReceive('getEntityData')
            ->with($entity)
            ->once()
            ->andReturn($entityData);
        $mapper->shouldReceive('toEntity')
            ->with($record, $entity)
            ->andReturn($entity);

        $unitOfWork = $this->getMockUnitOfWorkWithMapper($mapper);
        $unitOfWork->shouldReceive('setEntityRecord')
            ->with($entity, $record);
        $unitOfWork->shouldReceive('persistByTrackingPolicy')
            ->with($entity);
        $unitOfWork->shouldReceive('getEntityRecord')
            ->with($entity)
            ->andReturn($record);

        $persister = $this->getMockAbstractPersister($unitOfWork);

        if (array_key_exists('id', $entityData)) {
            $persister->shouldReceive('loadRecordById')
                ->with($entityData['id'], $entity)
                ->once()
                ->andReturn($record);
        }

        $persister->refresh($entity);
    }

    /**
     * @dataProvider recordProvider
     *
     * @param array|\stdClass $record
     */
    public function testCanDelete($record)
    {
        $entity = $this->getMockEntity();

        $mapper = $this->getMockMapper();
        $mapper->shouldReceive('fromEntity')
            ->with($entity, $record)
            ->once()
            ->andReturn($record);

        $unitOfWork = $this->getMockUnitOfWorkWithMapper($mapper);
        $unitOfWork->shouldReceive('getEntityRecord')
            ->with($entity)
            ->andReturn($record);
        $unitOfWork->shouldReceive('removeEntityRecord')
            ->with($entity)
            ->once();

        $persister = $this->getMockAbstractPersister($unitOfWork);
        $persister->shouldReceive('deleteRecord')
            ->with($record)
            ->once();

        $persister->delete($entity);
    }

    /**
     * @dataProvider canSaveProvider
     *
     * @param AdapterInterface $adapter
     * @param array|\stdClass $record
     */
    public function testCanSave($adapter, $record)
    {
        $entity = $this->getMockEntity();
        $mapper = $this->getMockMapper();
        $config = null;

        if ($adapter instanceof ManyToManyInterface) {
            $adapter->shouldReceive('insert')
                ->with('foo_bar', [
                    'foo_id' => 999,
                    'bar_id' => 1
                ])
                ->once();

            $metadata = $this->getMockEntityMetadata();
            $metadata->shouldReceive('hasRelationship')
                ->with('id')
                ->andReturn(false);
            $metadata->shouldReceive('hasRelationship')
                ->with('foo')
                ->andReturn(true);
            $metadata->shouldReceive('getRelationshipMetadata')
                ->andReturn([
                    'foo' => [
                        'type' => 'manyToMany',
                        'pivot' => 'foo_bar',
                        'localKey' => 'foo_id',
                        'foreignKey' => 'bar_id',
                    ]
                ]);

            $config = $this->getMockConfig();
            $config->shouldReceive('buildEntityMetadata')
                ->with($entity)
                ->andReturn($metadata);

            $relatedEntity = $this->getMockEntity();
            $relatedEntity->shouldReceive('getId')
                ->withNoArgs()
                ->andReturn(1);

            $mapper->shouldReceive('getEntityData')
                ->with($entity)
                ->andReturn(['id' => 1, 'foo' => $relatedEntity]);
        }

        $mapper->shouldReceive('fromEntity')
            ->with($entity, $record)
            ->once()
            ->andReturn($record);
        $mapper->shouldReceive('toEntity')
            ->with($record, $entity)
            ->once();

        $unitOfWork = $this->getMockUnitOfWorkWithMapper($mapper);
        $unitOfWork->shouldReceive('getEntityRecord')
            ->with($entity)
            ->andReturn($record);
        $unitOfWork->shouldReceive('setEntityRecord')
            ->with($entity, $record)
            ->twice();
        $unitOfWork->shouldReceive('removeEntityRecord')
            ->with($entity)
            ->once();
        $unitOfWork->shouldReceive('getAdapter')
            ->withNoArgs()
            ->andReturn($adapter);

        $persister = $this->getMockAbstractPersister($unitOfWork, $config);
        $persister->shouldReceive('saveRecord')
            ->with($record)
            ->once()
            ->andReturn($record);

        if ($adapter instanceof ManyToManyInterface) {
            $persister->shouldReceive('getRecordId')
                ->with($record)
                ->andReturn(999);
        }

        $persister->save($entity);
    }

    public function canSaveProvider()
    {
        return [
            [$this->getMockManyToManyAdapter(), new \stdClass()],
            [$this->getMockAdapter(), new \stdClass()],
            [$this->getMockManyToManyAdapter(), ['prop' => 'value']],
            [$this->getMockAdapter(), ['prop' => 'value']],
        ];
    }

    public function testSaveFailsForInvalidEntity()
    {
        $this->setExpectedException('\InvalidArgumentException');

        $entity = $this->getMockEntity();
        $mapper = $this->getMockMapper();
        $adapter = $this->getMockManyToManyAdapter();
        $record = new \stdClass();

        $adapter->shouldReceive('insert')->never();

        $metadata = $this->getMockEntityMetadata();
        $metadata->shouldReceive('hasRelationship')
            ->with('id')
            ->andReturn(false);
        $metadata->shouldReceive('hasRelationship')
            ->with('foo')
            ->andReturn(true);
        $metadata->shouldReceive('getRelationshipMetadata')
            ->andReturn([
                'foo' => [
                    'type' => 'manyToMany',
                    'pivot' => 'foo_bar',
                    'localKey' => 'foo_id',
                    'foreignKey' => 'bar_id',
                ]
            ]);

        $config = $this->getMockConfig();
        $config->shouldReceive('buildEntityMetadata')
            ->with($entity)
            ->andReturn($metadata);

        $relatedEntity = new \stdClass(); // invalid entity

        $mapper->shouldReceive('getEntityData')
            ->with($entity)
            ->andReturn(['id' => 1, 'foo' => $relatedEntity]);

        $mapper->shouldReceive('fromEntity')
            ->with($entity, $record)
            ->once()
            ->andReturn($record);

        $unitOfWork = $this->getMockUnitOfWorkWithMapper($mapper);
        $unitOfWork->shouldReceive('getEntityRecord')
            ->with($entity)
            ->andReturn($record);
        $unitOfWork->shouldReceive('setEntityRecord')
            ->with($entity, $record)
            ->once();
        $unitOfWork->shouldReceive('removeEntityRecord')
            ->with($entity)
            ->once();
        $unitOfWork->shouldReceive('getAdapter')
            ->withNoArgs()
            ->andReturn($adapter);

        $persister = $this->getMockAbstractPersister($unitOfWork, $config);
        $persister->shouldReceive('saveRecord')
            ->with($record)
            ->once()
            ->andReturn($record);

        $persister->shouldReceive('getRecordId')
            ->with($record)
            ->andReturn(999);

        $persister->save($entity);
    }

    /**
     * @return array
     */
    public function entityProvider()
    {
        return [
            [$this->getMockEntity(), ['id' => 1], new \stdClass()],
            [$this->getMockEntity(), ['id' => 1], []],
            [$this->getMockEntity(), ['foo' => 'bar'], new \stdClass()],
            [$this->getMockEntity(), ['foo' => 'bar'], []],
        ];
    }

    /**
     * @return array
     */
    public function recordProvider()
    {
        return [
            [new \stdClass()],
            [['prop' => 'value']]
        ];
    }

    /**
     * @param MapperInterface $mapper
     *
     * @return AbstractPersister
     */
    private function getMockAbstractPersisterWithMapper(MapperInterface $mapper)
    {
        $entityName = 'entityName';
        $recordName = 'recordName';

        $unitOfWork = $this->getMockUnitOfWorkWithMapper($mapper);
        $config = $this->getMockConfig();

        $persister = Mockery::mock(
            'Graze\Dal\Persister\AbstractPersister[saveRecord,deleteRecord,getRecordId,loadRecord,loadAllRecords,loadRecordById]',
            [
                $entityName,
                $recordName,
                $unitOfWork,
                $config
            ]
        )->shouldAllowMockingProtectedMethods();

        return $persister;
    }

    /**
     * @param MapperInterface $mapper
     *
     * @return UnitOfWorkInterface
     */
    private function getMockUnitOfWorkWithMapper(MapperInterface $mapper)
    {
        $entityName = 'entityName';

        $unitOfWork = $this->getMockUnitOfWork();
        $unitOfWork->shouldReceive('getMapper')
            ->with($entityName)
            ->andReturn($mapper);

        return $unitOfWork;
    }

    /**
     * @param UnitOfWorkInterface $unitOfWork
     * @param ConfigurationInterface $config
     *
     * @return AbstractPersister
     */
    private function getMockAbstractPersister(UnitOfWorkInterface $unitOfWork = null, ConfigurationInterface $config = null)
    {
        $entityName = 'entityName';
        $recordName = 'recordName';

        $unitOfWork = $unitOfWork ?: $this->getMockUnitOfWork();
        $config = $config ?: $this->getMockConfig();

        $persister = Mockery::mock(
            'Graze\Dal\Persister\AbstractPersister[saveRecord,deleteRecord,getRecordId,loadRecord,loadAllRecords,loadRecordById]',
            [
                $entityName,
                $recordName,
                $unitOfWork,
                $config
            ]
        )->shouldAllowMockingProtectedMethods();

        return $persister;
    }
}
