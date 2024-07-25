<?php

namespace Railken\EloquentSchema\Actions\Migration;

use Railken\EloquentSchema\ActionCase;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;

class CreateColumnAction extends Column
{
    protected AttributeBlueprint $attribute;

    protected array $result = [];

    public function __construct(AttributeBlueprint $attribute)
    {
        $this->attribute = $attribute;

        parent::__construct($attribute->model->table);
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
        return $this->migrate($this->attribute, ActionCase::Create);
    }

    public function migrateDown(): string
    {
        return $this->dropColumn($this->attribute);
    }

    public function migrate(): string
    {
        $migration = Column::$VarTable;

        $migration .= $this->migrateColumn($this->attribute);

        if ($this->attribute->required === false) {
            $migration .= $this->migrateNullable();
        }
        if ($this->attribute->default !== null) {
            $migration .= $this->migrateDefault($this->attribute->default);
        }

        return $migration.';';
    }
}
