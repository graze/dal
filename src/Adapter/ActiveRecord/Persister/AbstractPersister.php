<?php
/*
 * This file is part of Graze DAL
 *
 * Copyright (c) 2014 Nature Delivered Ltd. <http://graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see  http://github.com/graze/dal/blob/master/LICENSE
 */
namespace Graze\Dal\Adapter\ActiveRecord\Persister;

use Graze\Dal\Adapter\ActiveRecord\UnitOfWork;

/**
 * @deprecated - DAL 0.x
 */
abstract class AbstractPersister implements PersisterInterface
{
    protected $entityName;
    protected $recordName;
    protected $unitOfWork;

    /**
     * @param string $entityName
     * @param string $recordName
     * @param UnitOfWork $unitOfWork
     */
    public function __construct($entityName, $recordName, UnitOfWork $unitOfWork)
    {
        $this->entityName = $entityName;
        $this->recordName = $recordName;
        $this->unitOfWork = $unitOfWork;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityName()
    {
        return $this->entityName;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecordName()
    {
        return $this->recordName;
    }
}
