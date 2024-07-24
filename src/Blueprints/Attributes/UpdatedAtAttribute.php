<?php

namespace Railken\EloquentSchema\Blueprints\Attributes;

class UpdatedAtAttribute extends TimestampAttribute
{
    public ?string $cast = null;

    public function __construct(?string $name = null)
    {
        parent::__construct('updated_at');
        $this->nullable(true);
    }
}
