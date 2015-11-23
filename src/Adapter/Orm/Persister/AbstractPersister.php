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
namespace Graze\Dal\Adapter\Orm\Persister;

use Graze\Dal\Adapter\Orm\ConfigurationInterface;
use Graze\Dal\Adapter\Orm\UnitOfWork;

abstract class AbstractPersister implements PersisterInterface
{
    protected $entityName;
    protected $recordName;
    protected $unitOfWork;

    /**
     * @var ConfigurationInterface
     */
    protected $config;

    /**
     * @param string $entityName
     * @param string $recordName
     * @param UnitOfWork $unitOfWork
     * @param ConfigurationInterface $config
     */
    public function __construct($entityName, $recordName, UnitOfWork $unitOfWork, ConfigurationInterface $config)
    {
        $this->entityName = $entityName;
        $this->recordName = $recordName;
        $this->unitOfWork = $unitOfWork;
        $this->config = $config;
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
