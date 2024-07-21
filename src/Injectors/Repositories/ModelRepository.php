<?php

namespace Railken\EloquentSchema\Injectors\Repositories;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $newValuePlaceholder
 */
class ModelRepository extends Model
{
    protected function renameMutator(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $this->newValuePlaceholder,
            set: fn (?string $value) => $this->newValuePlaceholder = $value
        );
    }
}
