<?php

namespace Railken\EloquentSchema\Hooks;

use Railken\EloquentSchema\Blueprints\ModelBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

class TimestampsHook
{
    public function set(ClassEditor $classEditor, ModelBlueprint $modelBlueprint): void
    {
        if (! $modelBlueprint->hasAttributes(['created_at', 'updated_at'])) {
            $classEditor->setPublicPropertyValue('timestamps', false);
        } else {
            $classEditor->removeProperty('timestamps');
        }
    }
}
