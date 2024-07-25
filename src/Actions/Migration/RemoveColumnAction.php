<?php

namespace Railken\EloquentSchema\Actions\Migration;

use Railken\EloquentSchema\ActionCase;

class RemoveColumnAction extends CreateColumnAction
{
    public function getPrefix(): string
    {
        return 'remove_';
    }

    public function migrateUp(): string
    {
        return $this->dropColumn($this->newAttribute);
    }

    public function migrateDown(): string
    {
        return $this->migrate($this->newAttribute, ActionCase::Create);
    }
}
