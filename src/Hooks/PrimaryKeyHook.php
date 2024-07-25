<?php

namespace Railken\EloquentSchema\Hooks;

use Railken\EloquentSchema\Blueprints\ModelBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

class PrimaryKeyHook
{
    public function isPrimaryId(ModelBlueprint $modelBlueprint): bool
    {
        return $modelBlueprint->primaryKey[0] === 'id' && count($modelBlueprint->primaryKey) == 1;
    }

    public function serialize(ModelBlueprint $modelBlueprint)
    {
        return count($modelBlueprint->primaryKey) == 1 ? $modelBlueprint->primaryKey[0] : $modelBlueprint->primaryKey;
    }

    public function set(ClassEditor $classEditor, ModelBlueprint $modelBlueprint): void
    {
        if (! $this->isPrimaryId($modelBlueprint)) {
            $classEditor->setProtectedPropertyValue('primaryKey', $this->serialize($modelBlueprint));
        } else {
            $classEditor->removeProperty('primaryKey');
        }
    }
}
