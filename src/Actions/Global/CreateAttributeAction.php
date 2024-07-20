<?php

namespace Railken\EloquentSchema\Actions\Global;

use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

class CreateAttributeAction extends Attribute
{
    protected string $attribute;
    protected array $result = [];

    public function __construct(string $table, AttributeBlueprint $attribute)
    {
        $this->$attribute = $attribute;

        parent::__construct($table);
    }

    public function run(): void
    {
    }
}
