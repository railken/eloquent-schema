<?php

namespace Railken\EloquentSchema\Hooks;

use Railken\EloquentSchema\Blueprints\ModelBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

class TableHook
{
    public function set(ClassEditor $classEditor, ModelBlueprint $modelBlueprint): void
    {
        $classEditor->setProtectedPropertyValue('table', $modelBlueprint->table);
    }
}
