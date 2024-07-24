<?php

namespace Railken\EloquentSchema\Actions\Migration;

use Railken\EloquentSchema\ActionCase;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;

class UpdateColumnAction extends Column
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
        return $this->migrate($this->newAttribute, ActionCase::Update);
    }

    public function migrateDown(): string
    {
        return $this->migrate($this->oldAttribute, ActionCase::Update);
    }
}
