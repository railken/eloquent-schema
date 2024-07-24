<?php

namespace Railken\EloquentSchema\Actions\Migration;

use Exception;
use Railken\EloquentSchema\Blueprints\ModelBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

abstract class TableAction extends MigrationAction
{
    protected ClassEditor $classEditor;

    protected array $result = [];

    protected ModelBlueprint $model;

    public function __construct(ModelBlueprint $model)
    {
        $this->model = $model;
        parent::__construct($model->table);
    }

    /**
     * Populate the place-holders in the migration stub.
     *
     * @throws Exception
     */
    protected function render(string $up, string $down): string
    {
        return $this->renderMigration($up, $down);
    }

    abstract public function renderUp(): string;

    abstract public function renderDown(): string;
}
