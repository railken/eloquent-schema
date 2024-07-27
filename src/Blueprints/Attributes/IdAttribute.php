<?php

namespace Railken\EloquentSchema\Blueprints\Attributes;

use KitLoong\MigrationsGenerator\Enum\Migrations\Method\IndexType;

class IdAttribute extends IntegerAttribute
{
    public string $type = 'id';

    public static string $migration = 'id';

    public ?string $cast = null;

    public bool $dbNeedsName = false;

    public function __construct(?string $name = null)
    {
        parent::__construct('id');
    }

    public static function isMe($column, $params): bool
    {
        if ($column->getName() == 'id') {
            // Check that field is has an index primary
            $id = $params->getIndexes()->filter(function ($index) {
                return in_array('id', $index->getColumns()) && count($index->getColumns()) == 1 && $index->getType() == IndexType::PRIMARY;
            })->first();

            if (! empty($id)) {
                return true;
            }
        }

        return false;
    }
}
