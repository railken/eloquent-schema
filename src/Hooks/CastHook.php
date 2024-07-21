<?php

namespace Railken\EloquentSchema\Hooks;

use Railken\EloquentSchema\Actions\Eloquent\Attribute;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

class CastHook
{
    public function add(ClassEditor $classEditor, AttributeBlueprint $attribute): void
    {
        $classEditor->addProtectedProperty('casts', [$attribute->name => $attribute->type]);
    }

    public function remove(ClassEditor $classEditor, AttributeBlueprint $attribute): void
    {
        $classEditor->removeAttributeValueByIndex('casts', $attribute->name);
    }

    public function get(ClassEditor $classEditor, AttributeBlueprint $attribute): bool
    {
        return $classEditor->getAttributeByIndex("casts", $attribute->name);
    }

}
