<?php

namespace Railken\EloquentSchema\Actions\Migration;

use Illuminate\Support\Collection;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Exceptions\ColumnActionNoChangesFoundException;

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

    public function migrate(AttributeBlueprint $oldAttribute, AttributeBlueprint $newAttribute): ?string
    {
        $migration = ColumnAction::$VarTable.$this->migrateColumn($newAttribute);

        $changes = new Collection;

        self::callHooks('migrate', [$changes, $oldAttribute, $newAttribute]);

        if ($changes->isEmpty()) {
            throw new ColumnActionNoChangesFoundException($newAttribute->name, $newAttribute->model->table);
        }

        $migration .= $changes->implode('');

        $migration .= $this->migrateChange();

        return $migration.';';
    }
}
