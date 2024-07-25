<?php

namespace Railken\EloquentSchema\Actions\Migration;

use Railken\EloquentSchema\ActionCase;
use Railken\EloquentSchema\Blueprints\Attributes\CreatedAtAttribute;
use Railken\EloquentSchema\Blueprints\Attributes\IdAttribute;
use Railken\EloquentSchema\Blueprints\Attributes\UpdatedAtAttribute;
use Railken\EloquentSchema\Blueprints\ModelBlueprint;

class CreateTableAction extends TableAction
{
    protected ModelBlueprint $newModel;

    public function __construct(ModelBlueprint $newModel)
    {
        $this->newModel = $newModel;

        if (count($newModel->attributes) == 0) {
            $newModel->attributes([
                IdAttribute::make(),
                CreatedAtAttribute::make(),
                UpdatedAtAttribute::make(),
            ]);
        }

        parent::__construct($newModel->table);
    }

    public function renderUp(): string
    {
        return <<<EOD
        Schema::create('{$this->table}', function (Blueprint \$table) {
        {$this->migrateUp('    ')}
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

    public function migrateUp(?string $prefix = null): string
    {
        $columns = [];

        foreach ($this->newModel->attributes as $attribute) {
            $columns[] = $prefix.(new CreateColumnAction(
                $attribute
            ))->migrate($attribute, ActionCase::Create);
        }

        $primary = $this->migratePrimary($this->newModel->primaryKey);

        if (! empty($primary)) {
            $columns[] = $prefix.$primary;
        }

        return implode(PHP_EOL, $columns);
    }

    public function dropPrimary(): ?string
    {
        return Column::$VarTable.'->dropPrimary();';
    }

    public function migratePrimary(array $keys): ?string
    {
        if ($keys[0] !== 'id' || count($keys) > 1) {
            $primary = count($keys) == 1
                ? "'$keys[0]'"
                : str_replace('"', '\'', json_encode($keys));

            return Column::$VarTable."->primary($primary);";
        }

        return null;
    }
}
