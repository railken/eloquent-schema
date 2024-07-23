<?php

namespace Railken\EloquentSchema\Actions\Eloquent;

use Railken\EloquentSchema\Actions\Action;
use Railken\EloquentSchema\Blueprints\ModelBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;
use Railken\EloquentSchema\Hooks\HookManager;

abstract class ModelAction extends Action
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

    public function add(ModelBlueprint $model): void
    {
        self::callHooks('add', [$this->classEditor, $model]);
    }

    public function remove(ModelBlueprint $model): void
    {
        self::callHooks('remove', [$this->classEditor, $model]);
    }
}
