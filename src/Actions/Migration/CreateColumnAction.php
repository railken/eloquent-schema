<?php

namespace Railken\EloquentSchema\Actions\Migration;

use Illuminate\Support\Collection;
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
        $migration = ColumnAction::$VarTable.$this->migrateColumn($this->newAttribute);

        $changes = new Collection;

        self::callHooks('migrate', [$changes, null, $this->newAttribute]);

        $migration .= $changes->implode('');

        return $migration.';';
    }
}
