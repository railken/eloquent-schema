<?php

namespace Railken\EloquentSchema\Blueprints\Attributes;

class IntegerAttribute extends BaseAttribute
{
    public string $type = 'integer';

    public string $db = 'integer';
}
