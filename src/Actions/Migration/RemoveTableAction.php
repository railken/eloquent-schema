<?php

namespace Railken\EloquentSchema\Actions\Migration;

class RemoveTableAction extends CreateTableAction
{
    public function getPrefix(): string
    {
        return 'drop_';
    }

    public function renderUp(): string
    {
        return parent::renderDown();
    }

    public function renderDown(): string
    {
        return parent::renderUp();
    }
}
