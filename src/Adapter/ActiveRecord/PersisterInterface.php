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
namespace Graze\Dal\Adapter\ActiveRecord;

interface PersisterInterface
{
    /**
     * @return string
     */
    public function getEntityName();

    /**
     * @return string
     */
    public function getRecordName();

    /**
     * @param array $criteria
     * @param object $entity
     * @param array $orderBy
     * @return object
     */
    public function load(array $criteria, $entity = null, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     * @return object[]
     */
    public function loadAll(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param array $criteria
     * @param object $entity
     * @return object
     */
    public function loadById($id, $entity = null);

    /**
     * @param object $entity
     */
    public function refresh($entity);

    /**
     * @param object $entity
     */
    public function save($entity);
}
