<?php

namespace Railken\EloquentSchema\Actions\Migration;

class CreateTableAction extends TableAction
{
    public function renderUp(): string
    {
        return <<<EOD
        Schema::create('{$this->table}', function (Blueprint \$table) {
            \$table->id();
            \$table->timestamps();
        });
        EOD;
    }

    public function renderDown(): string
    {
        return <<<EOD
        Schema::dropTable('{$this->table}');
        EOD;
    }

    public function getPrefix(): string
    {
        return 'create_';
    }
}
