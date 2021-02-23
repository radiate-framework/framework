<?php

namespace Radiate\Support;

use Illuminate\Support\Collection as IlluminateCollection;

class Collection extends IlluminateCollection
{
    /**
     * Dump the items.
     *
     * @return $this
     */
    public function dump()
    {
        (new static(func_get_args()))
            ->push($this->all())
            ->each(function ($item) {
                var_dump($item);
            });

        return $this;
    }
}
