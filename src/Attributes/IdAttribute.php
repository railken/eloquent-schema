<?php

namespace Railken\EloquentSchema\Attributes;

class IdAttribute extends BaseAttribute
{
    public function migrateColumn()
    {
        return "->{$this->type}()";
    }

    public function migrateDrop()
    {
        return "->dropPrimary();\n".'$table->dropColumn'."('{$this->type}')";
    }
}
