<?php

namespace Railken\EloquentSchema\Hooks;

use Illuminate\Support\Collection;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;

class RequiredHook
{
    public function migrateNullable(): string
    {
        return '->nullable()';
    }

    public function migrate(Collection $changes, ?AttributeBlueprint $oldAttribute, AttributeBlueprint $newAttribute): void
    {
        if (($oldAttribute == null || $oldAttribute->required !== $newAttribute->required) && $newAttribute->required === false) {
            $changes->push($this->migrateNullable());
        }
    }

    public function updateBlueprintFromDatabase(AttributeBlueprint $attributeBlueprint, $column, $params): void
    {
        $attributeBlueprint->required($column->isNotNull());
    }
}
