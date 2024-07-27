<?php

namespace Railken\EloquentSchema\Hooks;

use Illuminate\Support\Collection;
use Railken\EloquentSchema\Actions\Migration\ColumnActionHookContract;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;

class DefaultHook implements ColumnActionHookContract
{
    public function migrateDefault($value): string
    {
        if (is_scalar($value)) {
            return "->default('{$value}')";
        } else {
            return '->default(null)';
        }
    }

    public function migrate(Collection $changes, ?AttributeBlueprint $oldAttribute, AttributeBlueprint $newAttribute): void
    {
        if (($oldAttribute == null && $newAttribute->default !== null) || ($oldAttribute !== null && $oldAttribute->default !== $newAttribute->default)) {
            $changes->push($this->migrateDefault($newAttribute->default));
        }
    }

    public function updateBlueprintFromDatabase(AttributeBlueprint $attributeBlueprint, $column, $params): void
    {
        $attributeBlueprint->default($column->getDefault());
    }
}
