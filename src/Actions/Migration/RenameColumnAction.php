<?php

namespace Railken\EloquentSchema\Actions\Migration;

use Railken\EloquentSchema\ActionCase;

class RenameColumnAction extends UpdateColumnAction
{
    public function migrateUp(): string
    {
        return $this->renameColumn($this->oldAttribute, $this->newAttribute);
    }

    public function migrateDown(): string
    {
        return $this->renameColumn($this->newAttribute, $this->oldAttribute);
    }

}
