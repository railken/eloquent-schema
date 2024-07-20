<?php

namespace Railken\EloquentSchema\Actions\Migration;

use Railken\EloquentSchema\ActionCase;
use Railken\EloquentSchema\Actions\Action;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

class CreateColumnAction extends Column
{
    protected AttributeBlueprint $attribute;
    protected array $result = [];

    public function __construct(string $table, ClassEditor $classEditor, AttributeBlueprint $attribute)
    {
        $this->attribute = $attribute;

        parent::__construct($table, $classEditor);
    }

    public function run(): void
    {
        $this->save();
    }

    public function getPrefix(): string
    {
        return "create_";
    }

    public function migrateUp(): string
    {
        return $this->attribute->migrate(ActionCase::Create);
    }

    public function migrateDown(): string
    {
        return $this->attribute->migrate(ActionCase::Remove);
    }
}
