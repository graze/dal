<?php

namespace Graze\Dal\Adapter\Orm\Persister;

interface PersisterInterface extends \Graze\Dal\Persister\PersisterInterface
{
    /**
     * @return string
     */
    public function getRecordName();
}
