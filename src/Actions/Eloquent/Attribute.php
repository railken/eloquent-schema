<?php

namespace Railken\EloquentSchema\Actions\Eloquent;

use Railken\EloquentSchema\Actions\Action;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;
use Railken\EloquentSchema\Hooks\HookManager;

abstract class Attribute extends Action
{
    use HookManager;

    protected ClassEditor $classEditor;

    protected array $result;

    public function __construct(ClassEditor $classEditor)
    {
        $this->classEditor = $classEditor;
    }

    public function getResult(): array
    {
        return $this->result;
    }

    public function save(): void
    {
        $this->result = $this->classEditor->save();
    }

    public function addToModel(AttributeBlueprint $attribute): void
    {
        self::callHooks('add', [$this->classEditor, $attribute]);
    }

    public function removeFromModel(AttributeBlueprint $attribute): void
    {
        self::callHooks('remove', [$this->classEditor, $attribute]);
    }
}
