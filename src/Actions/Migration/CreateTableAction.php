<?php

namespace Railken\EloquentSchema\Actions\Migration;

use Railken\EloquentSchema\ActionCase;

class CreateTableAction extends TableAction
{
    public function renderUp(): string
    {
        return <<<EOD
        Schema::create('{$this->table}', function (Blueprint \$table) {
            {$this->migrateUp()}
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

    public function migrateUp(): string
    {
        $content = '';

        foreach ($this->model->attributes as $attribute) {
            $content .= (new CreateColumnAction(
                $this->model->table,
                null,
                $attribute
            ))->migrate($attribute, ActionCase::Create);
        }

        return $content;
    }
}
