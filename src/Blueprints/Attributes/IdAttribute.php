<?php

namespace Railken\EloquentSchema\Blueprints\Attributes;

class IdAttribute extends BaseAttribute
{
    public string $type = 'id';

    public string $db = 'id';

    public ?string $cast = null;

    public bool $dbNeedsName = false;

    public function __construct(?string $name = null)
    {
        parent::__construct('id');
    }
}
