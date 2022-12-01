<?php

namespace Cels\Utilities\Database\Eloquent\Contracts;

interface WithCompositeKey
{
    /**
     * Get the composite primary keys for the model,
     * or "force" a composite key from primary key.
     *
     * @return array
     */
    public function getKeyNames();
}
