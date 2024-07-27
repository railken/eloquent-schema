<?php

namespace Railken\EloquentSchema\Blueprints\Attributes;

class CreatedAtAttribute extends TimestampAttribute
{
    public ?string $cast = null;

    public function __construct(?string $name = null)
    {
        parent::__construct('created_at');
        $this->required(false);
    }

    public static function isMe($column, $params): bool
    {
        return $column->getName() == 'created_at';
    }
}
