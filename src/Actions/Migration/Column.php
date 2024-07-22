<?php

namespace Railken\EloquentSchema\Actions\Migration;

use Exception;
use Illuminate\Support\Collection;
use Railken\EloquentSchema\ActionCase;
use Railken\EloquentSchema\Actions\Action;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

abstract class Column extends Action
{
    protected static string $VarTable = '$table';

    protected ClassEditor $classEditor;

    protected string $table;

    protected array $result = [];

    public function __construct(string $table, ClassEditor $classEditor)
    {
        $this->table = $table;
        $this->classEditor = $classEditor;
    }

    abstract public function getPrefix(): string;

    public function getResult(): array
    {
        return $this->result;
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
     * Save it.
     *
     * @throws Exception
     */
    public function save(): void
    {
        $up = $this->renderUp();
        $down = $this->renderDown();

        $render = $this->render($up, $down);
        file_put_contents($this->getPath(), $render);
        $this->result = [
            $this->getPath() => new Collection([
                'full' => $render,
                'up' => $up,
                'down' => $down,
            ]),
        ];
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

    public function renderMigration(string $up, string $down): string
    {
        return <<<EOD
        <?php
        
        use Illuminate\Database\Migrations\Migration;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Support\Facades\Schema;

        return new class() extends Migration
        {
            /**
             * Run the migrations.
             */
            public function up(): void
            {
                {$up}
            }

            /**
             * Reverse the migrations.
             */
            public function down(): void
            {
                {$down}
            }
        };
        EOD;
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
     * Get the full path to the migration.
     */
    protected function getPath(): string
    {
        $name = $this->getPrefix().$this->table.'_table';

        return database_path().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR.$this->getDatePrefix().'_'.$name.'.php';
    }

    /**
     * Get the date prefix for the migration.
     */
    protected function getDatePrefix(): string
    {
        return date('Y_m_d_His');
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
        $quotes = is_string($value) ? "'" : '';

        if (is_scalar($value)) {
            return "->default({$quotes}{$value}{$quotes})";
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
