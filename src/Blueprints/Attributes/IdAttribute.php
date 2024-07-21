<?php

namespace Railken\EloquentSchema\Blueprints\Attributes;

class IdAttribute extends BaseAttribute
{
    public string $type = 'id';

    public string $db = 'id';

    public bool $dbNeedsName = false;
}
