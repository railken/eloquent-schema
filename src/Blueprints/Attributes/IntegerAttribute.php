<?php

namespace Railken\EloquentSchema\Blueprints\Attributes;

class IntegerAttribute extends BaseAttribute
{
    public string $type = 'integer';

    public ?string $cast = 'integer';

    public static string $migration = 'integer';
}
