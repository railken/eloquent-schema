<?php

namespace Railken\EloquentSchema\Blueprints\Attributes;

class TextAttribute extends StringAttribute
{
    public string $type = 'string';

    public ?string $cast = 'string';

    public static string $migration = 'text';
}
