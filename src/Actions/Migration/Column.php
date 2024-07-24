<?php

namespace Railken\EloquentSchema\Actions\Migration;

use Exception;
use Railken\EloquentSchema\ActionCase;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;

abstract class Column extends MigrationAction
{
    protected static string $VarTable = '$table';

    protected array $result = [];

    public function __construct(string $table)
    {
        parent::__construct($table);
    }

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

    public function migrateNullable(): string
    {
        return '->nullable()';
    }

    public function migrateDefault($value): string
    {
        if (is_scalar($value)) {
            return "->default('{$value}')";
        } else {
            return '->default(null)';
        }
    }

    public function migrate(AttributeBlueprint $attribute, ActionCase $action): string
    {
        $migration = Column::$VarTable;

        if (in_array($action, [ActionCase::Create, ActionCase::Update])) {
            $migration .= $this->migrateColumn($attribute);

            if ($attribute->required === false) {
                $migration .= $this->migrateNullable();
            }
        }
        if (in_array($action, [ActionCase::Create])) {

            if ($attribute->default !== null) {
                $migration .= $this->migrateDefault($attribute->default);
            }
        }

        if (in_array($action, [ActionCase::Update])) {

            $migration .= $this->migrateDefault($attribute->default);

            $migration .= $this->migrateChange();
        }

        return $migration.';';
    }

    public function migrateColumn(AttributeBlueprint $attribute): string
    {
        return '->'.$attribute->db.($attribute->dbNeedsName ? "('{$attribute->name}')" : '()');
    }

    public function dropColumn(AttributeBlueprint $attribute): string
    {
        return Column::$VarTable."->dropColumn('{$attribute->name}');";
    }

    public function renameColumn(AttributeBlueprint $oldAttribute, AttributeBlueprint $newAttribute): string
    {
        return Column::$VarTable."->renameColumn('{$oldAttribute->name}', '{$newAttribute->name}');";
    }
}
