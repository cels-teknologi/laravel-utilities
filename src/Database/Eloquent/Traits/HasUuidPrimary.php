<?php

namespace Cels\Utilities\Database\Eloquent\Traits;

use Cels\Utilities\Database\Eloquent\Contracts\WithCompositeKey;
use Illuminate\Support\Str;

/** @deprecated  use `Illuminate\Database\Eloquent\Concerns\HasUuids` instead */
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
            if ($model instanceof WithCompositeKey || \in_array(HasCompositeKey::class, \class_uses_recursive($model), true)) {
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
