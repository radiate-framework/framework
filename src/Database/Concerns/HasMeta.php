<?php

namespace Radiate\Database\Concerns;

use Radiate\Database\Models\Meta;

trait HasMeta
{
    /**
     * The cached meta
     *
     * @var \Radiate\Database\Models\Meta
     */
    protected $cachedMeta;

    /**
     * Get the object meta
     *
     * @return \Radiate\Database\Models\Meta|null
     */
    public function getMetaAttribute()
    {
        if ($this->cachedMeta) {
            return $this->cachedMeta;
        }

        $meta = get_metadata($this->objectType, $this->getKey());

        if (!$meta) {
            return;
        }

        $this->cachedMeta = new Meta($this->unserializeMeta($meta));

        return $this->cachedMeta;
    }

    /**
     * Unserialize the object meta
     *
     * @param array $meta
     * @return array
     */
    protected function unserializeMeta(array $meta)
    {
        $unserialized = [];

        foreach ($meta as $key => $values) {
            $newValues = [];

            foreach ($values as $value) {
                $newValues[] = maybe_unserialize($value);
            }

            $unserialized[$key] = count($newValues) !== 1 ? $newValues : $newValues[0];
        }

        return $unserialized;
    }
}
