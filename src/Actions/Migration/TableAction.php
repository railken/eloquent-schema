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

    public function renderUp(): string
    {
        return <<<EOD
        Schema::create('{$this->table}', function (Blueprint \$table) {
            {$this->migrateUp()}
        });
        EOD;
    }

    public function renderDown(): string
    {
        return <<<EOD
        Schema::dropTable('{$this->table}');
        EOD;
    }

    public function dropTable(string $name): string
    {
        return <<<EOD
        \$table->dropTable('$name');
        EOD;
    }

    public function createTable(string $name): string
    {
        return <<<'EOD'
        $table->id();
            $table->timestamps();
        EOD;
    }
}
