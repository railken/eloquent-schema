<?php

namespace Railken\EloquentSchema\Blueprints\Attributes;

use Railken\EloquentSchema\Blueprints\AttributeBlueprint;

class BaseAttribute extends AttributeBlueprint
{
    public string $type = "string";
    public string $db = "string";
}
