<?php

namespace Railken\EloquentSchema\Hooks;

use KitLoong\MigrationsGenerator\Enum\Migrations\Method\IndexType;
use Railken\EloquentSchema\Actions\Eloquent\ModelActionHookContract;
use Railken\EloquentSchema\Blueprints\ModelBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

class PrimaryKeyHook implements ModelActionHookContract
{
    public function isPrimaryId(ModelBlueprint $modelBlueprint): bool
    {
        return $modelBlueprint->primaryKey[0] === 'id' && count($modelBlueprint->primaryKey) == 1;
    }

    public function serialize(ModelBlueprint $modelBlueprint)
    {
        return count($modelBlueprint->primaryKey) == 1 ? $modelBlueprint->primaryKey[0] : $modelBlueprint->primaryKey;
    }

    public function mutate(ClassEditor $classEditor, ModelBlueprint $modelBlueprint): void
    {
        if (! $this->isPrimaryId($modelBlueprint)) {
            $classEditor->setProtectedPropertyValue('primaryKey', $this->serialize($modelBlueprint));
        } else {
            $classEditor->removeProperty('primaryKey');
        }
    }

    public function updateBlueprintFromDatabase(ModelBlueprint $modelBlueprint, $params): void
    {
        $primaries = $params->getIndexes()->filter(function ($index) {
            return $index->getType() == IndexType::PRIMARY;
        })->first();

        if (! empty($primaries)) {
            $modelBlueprint->primaryKey($primaries->getColumns());
        }
    }
}
