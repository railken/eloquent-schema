<?php

namespace Railken\EloquentSchema\Actions\Eloquent;

use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

interface AttributeActionHookContract
{
    public function add(ClassEditor $classEditor, AttributeBlueprint $attribute): void;

    public function remove(ClassEditor $classEditor, AttributeBlueprint $attribute): void;

    public function set(ClassEditor $classEditor, AttributeBlueprint $attribute): void;
}
