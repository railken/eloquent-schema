<?php

namespace Railken\EloquentSchema\Actions\Migration;

use Railken\EloquentSchema\ActionCase;

class RenameColumnAction extends UpdateColumnAction
{
    public function migrateUp(): string
    {
        return $this->renameColumn($this->oldAttribute->name, $this->newAttribute->name);
    }

    public function migrateDown(): string
    {
        return $this->renameColumn($this->newAttribute->name, $this->oldAttribute->name);
    }

    public function renameColumn(string $old, string $new): string
    {
        return "\$table->renameColumn('{$old}', '{$new}')";
    }
}
