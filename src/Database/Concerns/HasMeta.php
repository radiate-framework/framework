<?php

namespace Radiate\Database\Concerns;

use Radiate\Database\Models\Meta;

trait HasMeta
{
    /**
     * The model meta object
     *
     * @var \Radiate\Database\Models\Meta
     */
    protected $meta;

    /**
     * Get the meta object
     *
     * @return \Radiate\Database\Models\Meta
     */
    public function meta()
    {
        if (!$this->meta) {
            $this->meta = (new Meta($this))->hydrate();
        }

        return $this->meta;
    }
}
