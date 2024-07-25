<?php

namespace Railken\EloquentSchema\Hooks;

use Railken\EloquentSchema\Blueprints\ModelBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

class IncrementingHook
{
    public function set(ClassEditor $classEditor, ModelBlueprint $modelBlueprint): void
    {
        if (! $modelBlueprint->incrementing) {
            $classEditor->setPublicPropertyValue('incrementing', false);
        } else {
            $classEditor->removeProperty('incrementing');
        }
    }
}
