<?php

namespace Railken\EloquentSchema\Hooks;

use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

class CastHook
{
    public function add(ClassEditor $classEditor, AttributeBlueprint $attribute): void
    {
        if ($attribute->cast !== null) {
            $classEditor->addProtectedProperty('casts', [$attribute->name => $attribute->cast]);
        }
    }

    public function remove(ClassEditor $classEditor, AttributeBlueprint $attribute): void
    {
        $classEditor->removeAttributeValueByIndex('casts', $attribute->name);
    }

    public function set(ClassEditor $classEditor, AttributeBlueprint $attribute)
    {
        // Type is already defined in the database, no need to redefine it
    }
}
