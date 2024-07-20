<?php

namespace Railken\EloquentSchema\Blueprints\Attributes;

class IdAttribute extends BaseAttribute
{
    public string $type = "id";
    public string $db = "id";

    public function migrateColumn(): string
    {
        return "->{$this->type}()";
    }

    /*public function migrateDrop(): string
    {
        return "->dropPrimary();\n".'$table->dropColumn'."('{$this->type}')";
    }*/
}
