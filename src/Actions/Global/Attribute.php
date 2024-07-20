<?php

namespace Railken\EloquentSchema\Actions\Global;

use Railken\EloquentSchema\Actions\Action;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

abstract class Attribute extends Action
{
    protected array $result = [];

    public function __construct(string $table)
    {
        $this->$table = $table;
    }

    public function getResult(): array
    {
        return $this->result;
    }

}
