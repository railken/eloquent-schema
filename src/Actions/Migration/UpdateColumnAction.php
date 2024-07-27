<?php

namespace Railken\EloquentSchema\Actions\Migration;

use Railken\EloquentSchema\Blueprints\AttributeBlueprint;

class UpdateColumnAction extends ColumnAction
{
    protected string $table;

    protected AttributeBlueprint $oldAttribute;

    protected AttributeBlueprint $newAttribute;

    public function __construct(AttributeBlueprint $oldAttribute, AttributeBlueprint $newAttribute)
    {
        $this->oldAttribute = $oldAttribute;
        $this->newAttribute = $newAttribute;

        parent::__construct($newAttribute->model->table);
    }

    public function getPrefix(): string
    {
        return 'update_';
    }

    public function migrateUp(): string
    {
        return $this->migrate($this->oldAttribute, $this->newAttribute);
    }

    public function migrateDown(): string
    {
        return $this->migrate($this->newAttribute, $this->oldAttribute);
    }

    public function migrate(AttributeBlueprint $oldAttribute, AttributeBlueprint $newAttribute): string
    {
        $migration = ColumnAction::$VarTable;

        $migration .= $this->migrateColumn($newAttribute);

        if ($oldAttribute->required !== $newAttribute->required && $newAttribute->required === false) {
            $migration .= $this->migrateNullable();
        }

        if ($oldAttribute->default !== $newAttribute->default) {
            $migration .= $this->migrateDefault($newAttribute->default);
        }

        $migration .= $this->migrateChange();

        return $migration.';';
    }
}
