<?php

namespace Railken\EloquentSchema\Actions\Migration;

use Railken\EloquentSchema\ActionCase;

class RemoveColumnAction extends CreateColumnAction
{
    public function getPrefix(): string
    {
        return "remove_";
    }

    public function migrateUp(): string
    {
        return $this->attribute->migrate(ActionCase::Remove);
    }

    public function migrateDown(): string
    {
        return $this->attribute->migrate(ActionCase::Create);
    }
}
