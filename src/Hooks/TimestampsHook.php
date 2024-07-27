<?php

namespace Railken\EloquentSchema\Hooks;

use Railken\EloquentSchema\Actions\Eloquent\ModelActionHookContract;
use Railken\EloquentSchema\Blueprints\ModelBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

class TimestampsHook implements ModelActionHookContract
{
    public function mutate(ClassEditor $classEditor, ModelBlueprint $modelBlueprint): void
    {
        if (! $modelBlueprint->hasAttributes(['created_at', 'updated_at'])) {
            $classEditor->setPublicPropertyValue('timestamps', false);
        } else {
            $classEditor->removeProperty('timestamps');
        }
    }

    public function updateBlueprintFromDatabase(ModelBlueprint $modelBlueprint, $params): void
    {
        // ...
    }
}
