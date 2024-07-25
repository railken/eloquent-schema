<?php

namespace Railken\EloquentSchema\Actions\Migration;

use Railken\EloquentSchema\ActionCase;
use Railken\EloquentSchema\Blueprints\ModelBlueprint;

class UpdateTableAction extends CreateTableAction
{
    protected ModelBlueprint $oldModel;

    protected ModelBlueprint $newModel;

    public function __construct(ModelBlueprint $oldModel, ModelBlueprint $newModel)
    {
        $this->oldModel = $oldModel;
        $this->newModel = $newModel;

        parent::__construct($newModel);
    }

    public function getPrefix(): string
    {
        return 'update_';
    }

    public function renderUp(): string
    {
        return <<<EOD
        Schema::table('{$this->table}', function (Blueprint \$table) {
        {$this->migrateUp('    ')}
        });
        EOD;
    }

    public function renderDown(): string
    {
        return <<<EOD
        Schema::table('{$this->table}', function (Blueprint \$table) {
        {$this->migrateDown('    ')}
        });
        EOD;
    }

    public function migrateFromTo(?string $prefix, ModelBlueprint $oldModel, ModelBlueprint $newModel): string
    {
        $columns = [];

        foreach ($newModel->diffAttributes($oldModel) as $attribute) {
            $columns[] = $prefix.(new CreateColumnAction($attribute))->migrate($attribute, ActionCase::Create);
        }

        foreach ($oldModel->diffAttributes($newModel) as $attribute) {
            $columns[] = $prefix.(new RemoveColumnAction($attribute))->dropColumn($attribute);
        }

        foreach ($oldModel->sameAttributes($newModel) as $diff) {
            if (! $diff->oldAttribute->equalsTo($diff->newAttribute)) {
                $columns[] = $prefix.(new UpdateColumnAction($diff->oldAttribute, $diff->newAttribute))
                    ->migrate($diff->newAttribute, ActionCase::Update);
            }
        }
        if ($oldModel->primaryKey !== $newModel->primaryKey) {
            $columns[] = $prefix.$this->dropPrimary();
            $primary = $this->migratePrimary($newModel->primaryKey);

            if (! empty($primary)) {
                $columns[] = $prefix.$primary;
            }
        }

        return implode(PHP_EOL, $columns);
    }

    public function migrateUp(?string $prefix = null): string
    {
        return $this->migrateFromTo($prefix, $this->oldModel, $this->newModel);
    }

    public function migrateDown(?string $prefix): string
    {
        return $this->migrateFromTo($prefix, $this->newModel, $this->oldModel);
    }
}
