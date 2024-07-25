<?php

namespace Railken\EloquentSchema\Actions\Eloquent;

use Railken\EloquentSchema\Actions\Action;
use Railken\EloquentSchema\Blueprints\ModelBlueprint;
use Railken\EloquentSchema\Hooks\HookManager;

abstract class ModelAction extends Action
{
    use HookManager;

    protected array $result;

    public function getResult(): array
    {
        return $this->result;
    }

    public function save(): void
    {
        $this->result = $this->classEditor->save();
    }

    public function set(ModelBlueprint $model): void
    {
        self::callHooks('set', [$this->classEditor, $model]);
    }
}
