<?php

namespace Railken\EloquentSchema\Actions\Migration;

class CreateTableAction extends TableAction
{
    public function migrateUp(): string
    {
        return $this->createTable($this->table);
    }

    public function migrateDown(): string
    {
        return $this->dropTable($this->table);
    }

    public function getPrefix(): string
    {
        return 'create_';
    }
}
