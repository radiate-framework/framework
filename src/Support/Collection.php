<?php

namespace Radiate\Support;

use Illuminate\Support\Collection as BaseCollection;

class Collection extends BaseCollection
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
