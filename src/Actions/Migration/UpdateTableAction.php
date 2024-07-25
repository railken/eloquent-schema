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

    public function migrateUp(?string $prefix = null): string
    {
        $columns = [];

        $attributesToAdd = array_diff(
            array_keys($this->newModel->attributes),
            array_keys($this->oldModel->attributes)
        );

        foreach ($attributesToAdd as $attributeName) {
            $columns[] = $prefix.(new CreateColumnAction(
                $this->newModel->getAttributeByName($attributeName)
            ))->migrate($this->newModel->getAttributeByName($attributeName), ActionCase::Create);
        }

        $attributesToRemove = array_diff(
            array_keys($this->oldModel->attributes),
            array_keys($this->newModel->attributes)
        );

        foreach ($attributesToRemove as $attributeName) {
            $columns[] = $prefix.(new RemoveColumnAction(
                $this->oldModel->getAttributeByName($attributeName)
            ))->dropColumn($this->oldModel->getAttributeByName($attributeName));
        }

        $attributesToUpdate = array_intersect(
            array_keys($this->newModel->attributes),
            array_keys($this->oldModel->attributes)
        );

        foreach ($attributesToUpdate as $attributeName) {

            $oldAttribute = $this->oldModel->getAttributeByName($attributeName);
            $newAttribute = $this->newModel->getAttributeByName($attributeName);

            if (! $oldAttribute->equalsTo($newAttribute)) {
                $columns[] = $prefix.(new UpdateColumnAction(
                    $oldAttribute,
                    $newAttribute
                ))->migrate($newAttribute, ActionCase::Update);
            }
        }

        // Check diffs with primaries

        if ($this->oldModel->primaryKey !== $this->newModel->primaryKey) {
            $columns[] = $prefix.$this->dropPrimary();
            $primary = $this->migratePrimary($this->newModel->primaryKey);

            if (! empty($primary)) {
                $columns[] = $prefix.$primary;
            }
        }

        return implode(PHP_EOL, $columns);
    }

    public function migrateDown(string $prefix)
    {
        $columns = [];

        $attributesToAdd = array_diff(
            array_keys($this->oldModel->attributes),
            array_keys($this->newModel->attributes)
        );

        foreach ($attributesToAdd as $attributeName) {
            $columns[] = $prefix.(new CreateColumnAction(
                $this->oldModel->getAttributeByName($attributeName)
            ))->migrate($this->oldModel->getAttributeByName($attributeName), ActionCase::Create);
        }

        $attributesToRemove = array_diff(
            array_keys($this->newModel->attributes),
            array_keys($this->oldModel->attributes)
        );

        foreach ($attributesToRemove as $attributeName) {
            $columns[] = $prefix.(new RemoveColumnAction(
                $this->newModel->getAttributeByName($attributeName)
            ))->dropColumn($this->newModel->getAttributeByName($attributeName));
        }

        $attributesToUpdate = array_intersect(
            array_keys($this->newModel->attributes),
            array_keys($this->oldModel->attributes)
        );

        foreach ($attributesToUpdate as $attributeName) {

            $oldAttribute = $this->oldModel->getAttributeByName($attributeName);
            $newAttribute = $this->newModel->getAttributeByName($attributeName);

            if (! $oldAttribute->equalsTo($newAttribute)) {
                $columns[] = $prefix.(new UpdateColumnAction(
                    $newAttribute,
                    $oldAttribute
                ))->migrate($newAttribute, ActionCase::Update);
            }
        }

        // Check diffs with primaries

        if ($this->oldModel->primaryKey !== $this->newModel->primaryKey) {
            $columns[] = $prefix.$this->dropPrimary();
            $primary = $this->migratePrimary($this->oldModel->primaryKey);

            if (! empty($primary)) {
                $columns[] = $prefix.$primary;
            }
        }

        return implode(PHP_EOL, $columns);
    }
}
