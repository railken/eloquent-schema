<?php

namespace Railken\EloquentSchema\Actions\Migration;

use Railken\EloquentSchema\ActionCase;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;

class CreateColumnAction extends ColumnAction
{
    protected AttributeBlueprint $newAttribute;

    protected array $result = [];

    public function __construct(AttributeBlueprint $newAttribute)
    {
        $this->newAttribute = $newAttribute;

        parent::__construct($newAttribute->model->table);
    }

    public function run(): void
    {
        $this->save();
    }

    public function getPrefix(): string
    {
        return 'create_';
    }

    public function migrateUp(): string
    {
        return $this->migrate($this->newAttribute, ActionCase::Create);
    }

    public function migrateDown(): string
    {
        return $this->dropColumn($this->newAttribute);
    }

    public function migrate(): string
    {
        $migration = ColumnAction::$VarTable;

        $migration .= $this->migrateColumn($this->newAttribute);

        if ($this->newAttribute->required === false) {
            $migration .= $this->migrateNullable();
        }
        if ($this->newAttribute->default !== null) {
            $migration .= $this->migrateDefault($this->newAttribute->default);
        }

        return $migration.';';
    }
}
