<?php

namespace Railken\EloquentSchema\Hooks;

use Railken\EloquentSchema\Actions\Eloquent\Attribute;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

class GuardedHook
{
    public function add(ClassEditor $classEditor, AttributeBlueprint $attribute): void
    {
        if (!$attribute->fillable) {
            $classEditor->addProtectedProperty("guarded", $attribute->name);
        }
    }

    public function remove(ClassEditor $classEditor, AttributeBlueprint $attribute): void
    {
        $classEditor->removePropertyValue('guarded', $attribute->name);
    }

    public function set(ClassEditor $classEditor, AttributeBlueprint $attribute): void
    {
        // FillableHook will take care of filling the value of fillable
    }

}
