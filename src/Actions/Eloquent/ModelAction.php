<?php

namespace Railken\EloquentSchema\Actions\Eloquent;

use Railken\EloquentSchema\Actions\Action;
use Railken\EloquentSchema\Blueprints\ModelBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;
use Railken\EloquentSchema\Hooks\HookManager;
use Railken\EloquentSchema\Support;

abstract class ModelAction extends Action
{
    use HookManager;

    protected ClassEditor $classEditor;

    protected ModelBlueprint $model;

    protected array $result;

    public function __construct(ModelBlueprint $model)
    {
        $this->model = $model;

        if (isset($model->instance)) {
            $this->classEditor = new ClassEditor(Support::getPathByObject($model->instance));
        } else {
            $this->classEditor = ClassEditor::newClass($model->class, $model->workingDir);
        }
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
