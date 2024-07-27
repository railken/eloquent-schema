<?php

namespace Railken\EloquentSchema\Actions\Migration;

use Exception;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Hooks\HookManager;

abstract class ColumnAction extends MigrationAction
{
    use HookManager;

    public static string $VarTable = '$table';

    protected array $result = [];

    /**
     * Populate the place-holders in the migration stub.
     *
     * @throws Exception
     */
    public function run(): void
    {
        $this->save();
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
        Schema::table('{$this->table}', function (Blueprint \$table) {
            {$this->migrateUp()}
        });
        EOD;
    }

    public function renderDown(): string
    {
        return <<<EOD
        Schema::table('{$this->table}', function (Blueprint \$table) {
            {$this->migrateDown()}
        });
        EOD;
    }

    /**
     * Get the migration stub file.
     */
    protected function getStub(): string
    {
        return file_get_contents(__DIR__.'/../../../stubs/migration.attribute.stub');
    }

    abstract protected function migrateDown(): string;

    abstract protected function migrateUp(): string;

    public function migrateChange(): string
    {
        return '->change()';
    }

    public function migrateColumn(AttributeBlueprint $attribute): string
    {
        return '->'.$attribute::$migration.($attribute->dbNeedsName ? "('{$attribute->name}')" : '()');
    }

    public function dropColumn(AttributeBlueprint $attribute): string
    {
        return ColumnAction::$VarTable."->dropColumn('{$attribute->name}');";
    }

    public function renameColumn(AttributeBlueprint $oldAttribute, AttributeBlueprint $newAttribute): string
    {
        return ColumnAction::$VarTable."->renameColumn('{$oldAttribute->name}', '{$newAttribute->name}');";
    }
}
