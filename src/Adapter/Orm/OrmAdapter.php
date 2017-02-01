<?php
/*
 * This file is part of Graze DAL
 *
 * Copyright (c) 2017 Nature Delivered Ltd. <http://graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see  http://github.com/graze/dal/blob/master/LICENSE
 */
namespace Graze\Dal\Adapter\Orm;

use Closure;
use Graze\Dal\Adapter\AbstractAdapter;
use Graze\Dal\Relationship\ManyToManyInterface;

abstract class OrmAdapter extends AbstractAdapter implements OrmAdapterInterface, ManyToManyInterface
{
    /**
     * @param callable $fn
     */
    public function transaction(callable $fn)
    {
        if (! $fn instanceof Closure) {
            $fn = function ($adapter) use ($fn) {
                call_user_func($fn, $adapter);
            };
        }

        $this->beginTransaction();

        try {
            $fn($this);
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
}
