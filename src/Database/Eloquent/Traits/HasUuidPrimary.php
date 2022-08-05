<?php

namespace Cels\Utilities\Database\Eloquent\Traits;

use Illuminate\Support\Str;

trait HasUuidPrimary
{
    /**
     * Generates UUID for a given column name / key name.
     *
     * @param  string  $keyName 
     * @return string
     */
    public function generateUuid($keyName = null)
    {
        return Str::orderedUuid();
    }

    /**
     * Boot UUID primary trait.
     *
     * @return void
     */
    protected static function bootHasUuidPrimary()
    {
        static::creating(function ($model) {
            if ($model instanceof HasCompositeKey) {
                $keys = $model->getKeyNames();
                foreach ($keys as $keyName) {
                    if (!empty($model->{$keyName})) {
                        continue;
                    }

                    $model->{$keyName} = (string) $model->generateUuid($keyName);
                }
            }
            else {
                $key = $model->getKeyName();
                $model->{$key} = (string) $model->generateUuid($key);
            }
        });
    }

    /**
     * Initialize HasUuidPrimary trait
     * 
     * @return void
     */
    protected function initializeHasUuidPrimary()
    {
        $this->incrementing = false;
        $this->keyType = 'string';
    }
}
