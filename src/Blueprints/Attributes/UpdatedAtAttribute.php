<?php

namespace Railken\EloquentSchema\Blueprints\Attributes;

class UpdatedAtAttribute extends TimestampAttribute
{
    public ?string $cast = null;

    public function __construct(?string $name = null)
    {
        parent::__construct('updated_at');
        $this->required(false);
    }

    public static function isMe($column, $params): bool
    {
        return $column->getName() == 'updated_at';
    }
}
