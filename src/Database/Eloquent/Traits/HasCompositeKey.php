<?php

namespace Cels\Utilities\Database\Eloquent\Traits;

use InvalidArgumentException;

trait HasCompositeKey
{
    /**
     * Get the composite primary keys for the model,
     * or "force" a composite key from primary key.
     *
     * @return array
     */
    public function getKeyNames()
    {
        if (!is_array($this->primaryKey)) {
            return [$this->primaryKey, ];
        }

        return $this->primaryKey;
    }

    /**
     * Set the keys for a save update query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery($query)
    {
        if (!is_array($this->primaryKey)) {
            return parent::setKeysForSaveQuery($query);
        }

        $keys = $this->getKeyNames();
        foreach ($keys as $keyName) {
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    /**
     * Get the composite key values for a save query.
     *
     * @param ?string $keyName
     * @return mixed
     * @throws InvalidArgumentException
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if (is_null($keyName)) {
            throw new InvalidArgumentException('\'keyName\' argument cannot be null for models with composite key.');
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }
}
