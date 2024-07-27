<?php

namespace Railken\EloquentSchema\Hooks;

use Railken\EloquentSchema\Blueprints\ModelBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

class IncrementingHook
{
    public function mutate(ClassEditor $classEditor, ModelBlueprint $modelBlueprint): void
    {
        if (! $modelBlueprint->incrementing) {
            $classEditor->setPublicPropertyValue('incrementing', false);
        } else {
            $classEditor->removeProperty('incrementing');
        }
    }

    public function updateBlueprintFromDatabase(ModelBlueprint $modelBlueprint, $params)
    {
        // ...
    }
}
