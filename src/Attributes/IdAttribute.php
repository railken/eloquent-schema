<?php

namespace Railken\EloquentSchema\Attributes;

class IdAttribute extends BaseAttribute
{
    public function migrateColumn()
    {
        return "->{$this->type}()";
    }
}
