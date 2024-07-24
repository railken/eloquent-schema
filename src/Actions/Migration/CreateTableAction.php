<?php

namespace Railken\EloquentSchema\Actions\Migration;

use Railken\EloquentSchema\ActionCase;
use Railken\EloquentSchema\Blueprints\Attributes\CreatedAtAttribute;
use Railken\EloquentSchema\Blueprints\Attributes\IdAttribute;
use Railken\EloquentSchema\Blueprints\Attributes\UpdatedAtAttribute;
use Railken\EloquentSchema\Blueprints\ModelBlueprint;

class CreateTableAction extends TableAction
{
    public function __construct(ModelBlueprint $model)
    {
        if (count($model->attributes) == 0) {
            $model->attributes([
                IdAttribute::make(),
                CreatedAtAttribute::make(),
                UpdatedAtAttribute::make(),
            ]);
        }

        parent::__construct($model);
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

        foreach ($this->model->attributes as $attribute) {
            $columns[] = $prefix.(new CreateColumnAction(
                $attribute
            ))->migrate($attribute, ActionCase::Create);
        }

        $primary = $this->migratePrimary($this->model->primaryKey);

        if (! empty($primary)) {
            $columns[] = $prefix.$primary;
        }

        return implode(PHP_EOL, $columns);
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
