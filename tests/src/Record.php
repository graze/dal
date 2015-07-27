<?php

namespace Graze\Dal\Test;

class Record
{
    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'foo' => 'bar'
        ];
    }

    /**
     * @param array $data
     */
    public function fromArray(array $data)
    {
        $this->foo = $data['foo'];
    }
}
