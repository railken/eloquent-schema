<?php

namespace Railken\EloquentSchema\Attributes;

class IdAttribute extends BaseAttribute
{
    public function migrateColumn(): string
    {
        return "->{$this->type}()";
    }

    /*public function migrateDrop(): string
    {
        return "->dropPrimary();\n".'$table->dropColumn'."('{$this->type}')";
    }*/
}
