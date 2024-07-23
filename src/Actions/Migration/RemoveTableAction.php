<?php

namespace Railken\EloquentSchema\Actions\Migration;

class RemoveTableAction extends CreateTableAction
{
    public function getPrefix(): string
    {
        return 'drop_';
    }
}
