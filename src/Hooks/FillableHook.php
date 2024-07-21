<?php

namespace Railken\EloquentSchema\Hooks;

use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

class FillableHook
{
    public function add(ClassEditor $classEditor, AttributeBlueprint $attribute): void
    {
        if ($attribute->fillable) {
            $classEditor->addProtectedProperty('fillable', $attribute->name);
        }
    }

    public function remove(ClassEditor $classEditor, AttributeBlueprint $attribute): void
    {
        $classEditor->removePropertyValue('fillable', $attribute->name);
    }

    public function set(ClassEditor $classEditor, AttributeBlueprint $attribute): void
    {
        $attribute->fillable($classEditor->isValueInAttribute('fillable', $attribute->name));
    }
}
