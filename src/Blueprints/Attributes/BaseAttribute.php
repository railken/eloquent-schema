<?php

namespace Railken\EloquentSchema\Blueprints\Attributes;

use Railken\EloquentSchema\Blueprints\AttributeBlueprint;

class BaseAttribute extends AttributeBlueprint
{
    public string $type = 'string';

    public ?string $cast = 'string';

    public static string $migration = 'string';

    public bool $dbNeedsName = true;

    public static function isMe($column, $params): bool
    {
        return $column->getType()->value == static::$migration;
    }
}
