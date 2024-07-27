<?php

namespace Railken\EloquentSchema\Hooks;

use Railken\EloquentSchema\Actions\Eloquent\ModelActionHookContract;
use Railken\EloquentSchema\Blueprints\ModelBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

class TableHook implements ModelActionHookContract
{
    public function mutate(ClassEditor $classEditor, ModelBlueprint $modelBlueprint): void
    {
        $classEditor->setProtectedPropertyValue('table', $modelBlueprint->table);
    }

    public function updateBlueprintFromDatabase(ModelBlueprint $modelBlueprint, $params): void
    {
        // table already defined inside the model
    }
}
