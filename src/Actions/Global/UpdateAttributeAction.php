<?php

namespace Railken\EloquentSchema\Actions\Global;

use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

class UpdateAttributeAction extends Attribute
{
    protected string $table;

    protected AttributeBlueprint $oldAttribute;
    protected AttributeBlueprint $newAttribute;

    public function __construct(string $table, AttributeBlueprint $oldAttribute, AttributeBlueprint $newAttribute)
    {
        $this->oldAttribute = $oldAttribute;
        $this->newAttribute = $newAttribute;

        parent::__construct($table);
    }

    public function run(): void
    {
    }
}
