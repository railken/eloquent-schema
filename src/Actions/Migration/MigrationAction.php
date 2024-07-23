<?php

namespace Railken\EloquentSchema\Actions\Migration;

use Exception;
use Illuminate\Support\Collection;
use Railken\EloquentSchema\Actions\Action;

abstract class MigrationAction extends Action
{
    protected string $table;

    protected array $result = [];

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function getResult(): array
    {
        return $this->result;
    }

    abstract public function getPrefix(): string;

    /**
     * Populate the place-holders in the migration stub.
     *
     * @throws Exception
     */
    public function run(): void
    {
        $this->save();
    }

    /**
     * Save it.
     *
     * @throws Exception
     */
    public function save(): void
    {
        $up = $this->renderUp();
        $down = $this->renderDown();

        $render = $this->render($up, $down);
        file_put_contents($this->getPath(), $render);
        $this->result = [
            $this->getPath() => new Collection([
                'full' => $render,
                'up' => $up,
                'down' => $down,
            ]),
        ];
    }

    /**
     * Get the full path to the migration.
     */
    protected function getPath(): string
    {
        $name = $this->getPrefix().$this->table.'_table';

        return database_path().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR.$this->getDatePrefix().'_'.$name.'.php';
    }

    /**
     * Get the date prefix for the migration.
     */
    protected function getDatePrefix(): string
    {
        return date('Y_m_d_His');
    }

    public function renderMigration(string $up, string $down): string
    {
        return <<<EOD
        <?php
        
        use Illuminate\Database\Migrations\Migration;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Support\Facades\Schema;

        return new class() extends Migration
        {
            /**
             * Run the migrations.
             */
            public function up(): void
            {
                {$up}
            }

            /**
             * Reverse the migrations.
             */
            public function down(): void
            {
                {$down}
            }
        };
        EOD;
    }

    abstract protected function migrateDown(): string;

    abstract protected function migrateUp(): string;
}
