<?php

namespace Railken\EloquentSchema\Blueprints\Attributes;

class StringAttribute extends BaseAttribute
{
    public string $type = 'string';

    public ?string $cast = 'string';

    public string $db = 'string';
}
