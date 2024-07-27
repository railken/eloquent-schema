<?php

namespace Railken\EloquentSchema\Actions\Eloquent;

use Railken\EloquentSchema\Blueprints\ModelBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

interface ModelActionHookContract
{
    public function mutate(ClassEditor $classEditor, ModelBlueprint $modelBlueprint): void;

    public function updateBlueprintFromDatabase(ModelBlueprint $modelBlueprint, $params): void;
}
