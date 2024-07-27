<?php

namespace Railken\EloquentSchema\Hooks;

use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

class RequiredHook
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
        // Type is already defined in the database, no need to redefine it from the Model
    }

    public function updateBlueprintFromDatabase(AttributeBlueprint $attributeBlueprint, $column, $params)
    {
        $attributeBlueprint->required($column->isNotNull());
    }
}
