<?php

namespace Railken\EloquentSchema\Actions\Global;

use Railken\EloquentSchema\Actions\Action;

abstract class Attribute extends Action
{
    protected array $result = [];

    public function __construct(string $table)
    {
        $this->$table = $table;
    }

    public function getResult(): array
    {
        return $this->result;
    }
}
