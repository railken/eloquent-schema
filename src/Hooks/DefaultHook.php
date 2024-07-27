<?php

namespace Railken\EloquentSchema\Hooks;

use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

class DefaultHook
{
    public function add(ClassEditor $classEditor, AttributeBlueprint $attribute): void
    {
        // ...
    }

    public function remove(ClassEditor $classEditor, AttributeBlueprint $attribute): void
    {
        // ...
    }

    public function set(ClassEditor $classEditor, AttributeBlueprint $attribute)
    {
        // ...
    }

    public function updateBlueprintFromDatabase(AttributeBlueprint $attributeBlueprint, $column, $params)
    {
        $attributeBlueprint->default($column->getDefault());
    }
}
