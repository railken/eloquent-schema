<?php

namespace Railken\EloquentSchema\Actions\Migration;

use Railken\EloquentSchema\Blueprints\ModelBlueprint;
use Railken\EloquentSchema\Exceptions\ColumnActionNoChangesFoundException;

class UpdateTableAction extends CreateTableAction
{
    protected ModelBlueprint $oldModel;

    public function __construct(ModelBlueprint $oldNewModel, ModelBlueprint $newModel)
    {
        $this->oldModel = $oldNewModel;

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
            $columns[] = $prefix.(new CreateColumnAction($attribute))->migrate();
        }

        foreach ($oldModel->diffAttributes($newModel) as $attribute) {
            $columns[] = $prefix.(new RemoveColumnAction($attribute))->dropColumn($attribute);
        }

        foreach ($oldModel->sameAttributes($newModel) as $diff) {

            try {
                $columns[] = $prefix.(new UpdateColumnAction($diff->oldAttribute, $diff->newAttribute))
                    ->migrate($diff->oldAttribute, $diff->newAttribute);
            } catch (ColumnActionNoChangesFoundException $e) {

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
