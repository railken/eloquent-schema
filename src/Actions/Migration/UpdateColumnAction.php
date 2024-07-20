<?php

namespace Railken\EloquentSchema\Actions\Migration;

use Railken\EloquentSchema\ActionCase;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

class UpdateColumnAction extends Column
{
    protected string $table;

    protected AttributeBlueprint $oldAttribute;
    protected AttributeBlueprint $newAttribute;

    public function __construct(string $table, ClassEditor $classEditor, AttributeBlueprint $oldAttribute, AttributeBlueprint $newAttribute)
    {
        $this->oldAttribute = $oldAttribute;
        $this->newAttribute = $newAttribute;

        parent::__construct($table, $classEditor);
    }

    public function getPrefix(): string
    {
        return "update_";
    }

    public function migrateUp(): string
    {
        // Handling renaming...
        return $this->newAttribute->migrate(ActionCase::Update);
    }

    public function migrateDown(): string
    {
        return $this->oldAttribute->migrate(ActionCase::Update);
    }
}
