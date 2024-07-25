<?php

namespace Railken\EloquentSchema\Actions\Migration;

use Exception;

abstract class TableAction extends MigrationAction
{
    protected array $result = [];

    /**
     * Populate the place-holders in the migration stub.
     *
     * @throws Exception
     */
    protected function render(string $up, string $down): string
    {
        return $this->renderMigration($up, $down);
    }

    abstract public function renderUp(): string;

    abstract public function renderDown(): string;
}
