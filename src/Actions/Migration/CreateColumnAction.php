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
}
