<?php

namespace Railken\EloquentSchema\Hooks;

use Railken\EloquentSchema\Actions\Eloquent\AttributeActionHookContract;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

class CastHook implements AttributeActionHookContract
{
    public function add(ClassEditor $classEditor, AttributeBlueprint $attribute): void
    {
        if ($attribute->cast !== null) {
            $classEditor->addProtectedPropertyValue('casts', [$attribute->name => $attribute->cast]);
        }
    }

    public function remove(ClassEditor $classEditor, AttributeBlueprint $attribute): void
    {
        $classEditor->removeAttributeValueByIndex('casts', $attribute->name);
    }

    public function set(ClassEditor $classEditor, AttributeBlueprint $attribute): void
    {
        $attribute->cast($classEditor->getValueInAttribute('casts', $attribute->name));
    }
}
