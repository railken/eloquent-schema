<?php

namespace Railken\EloquentSchema\Blueprints\Attributes;

class TimestampAttribute extends BaseAttribute
{
    public string $type = 'date';

    public string $db = 'timestamp';
}
