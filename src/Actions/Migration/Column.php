<?php

namespace Railken\EloquentSchema\Actions\Migration;

use Exception;
use Railken\EloquentSchema\ActionCase;
use Railken\EloquentSchema\Actions\Action;
use Railken\EloquentSchema\Actions\Global\Attribute;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Builders\MigrationBuilder;
use Railken\EloquentSchema\Editors\ClassEditor;
use Railken\Template\Generators;

abstract class Column extends Action
{
    protected static string $VarTable = "\$table";

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
        $render = $this->render();
        file_put_contents($this->getPath(), $this->render());
        $this->result = [$this->getPath() => $render];
    }

    /**
     * Populate the place-holders in the migration stub.
     *
     * @throws Exception
     */
    protected function render(): string
    {
        $generator = new Generators\HtmlGenerator();

        return $generator->generateAndRender($this->getStub(), [
            'table' => $this->table,
            'oldValue' => $this->migrateDown(),
            'newValue' => $this->migrateUp()
        ]);
    }

    /**
     * Get the full path to the migration.
     *
     * @return string
     */
    protected function getPath(): string
    {
        $name = $this->getPrefix().$this->table."_table";

        return database_path().DIRECTORY_SEPARATOR."migrations".DIRECTORY_SEPARATOR.$this->getDatePrefix().'_'.$name.'.php';
    }

    /**
     * Get the date prefix for the migration.
     *
     * @return string
     */
    protected function getDatePrefix(): string
    {
        return date('Y_m_d_His');
    }

    /**
     * Get the migration stub file.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return file_get_contents(__DIR__."/../../../stubs/migration.attribute.stub");
    }

    abstract protected function migrateDown(): string;
    abstract protected function migrateUp(): string;

    public function migrateChange(): string
    {
        return "->change()";
    }

    public function migrateNullable(): string
    {
        return "->nullable()";
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

        if (in_array($action, [ActionCase::Update])) {
            $migration .= $this->migrateChange();
        }

        return $migration.";";
    }

    public function migrateColumn(AttributeBlueprint $attribute): string
    {
        return "->".$attribute->db.($attribute->dbNeedsName ? "('{$attribute->name}')" : "()");
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
