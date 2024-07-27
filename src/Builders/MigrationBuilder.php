<?php

namespace Railken\EloquentSchema\Builders;

use Exception;
use Railken\EloquentSchema\Actions\Migration\ColumnAction;
use Railken\EloquentSchema\Actions\Migration\CreateColumnAction;
use Railken\EloquentSchema\Actions\Migration\CreateTableAction;
use Railken\EloquentSchema\Actions\Migration\RemoveColumnAction;
use Railken\EloquentSchema\Actions\Migration\RemoveTableAction;
use Railken\EloquentSchema\Actions\Migration\RenameColumnAction;
use Railken\EloquentSchema\Actions\Migration\UpdateColumnAction;
use Railken\EloquentSchema\Actions\Migration\UpdateTableAction;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Blueprints\ModelBlueprint;

class MigrationBuilder extends Builder
{
    protected AttributeBlueprint $attribute;

    public function createModel(ModelBlueprint $modelBlueprint): CreateTableAction
    {
        return new CreateTableAction($modelBlueprint);
    }

    public function removeModel(ModelBlueprint $modelBlueprint): RemoveTableAction
    {
        return new RemoveTableAction($modelBlueprint);
    }

    public function fillBlueprintFromCurrentStatus(ModelBlueprint $modelBlueprint)
    {
        $params = $this->schemaRetriever->getMigrationGeneratorSchema()->getTable($modelBlueprint->table);

        foreach ($params->getColumns() as $column) {
            $attributeBlueprint = $modelBlueprint->getAttributeByName($column->getName());
            ColumnAction::callHooks('updateBlueprintFromDatabase', [$attributeBlueprint, $column, $params]);
        }

    }

    public function updateModel(ModelBlueprint $oldModelBlueprint, ModelBlueprint $newModelBlueprint): UpdateTableAction
    {
        return new UpdateTableAction($oldModelBlueprint, $newModelBlueprint);
    }

    public function createAttribute(ModelBlueprint $modelBlueprint, AttributeBlueprint $attributeBlueprint): CreateColumnAction
    {
        return new CreateColumnAction($attributeBlueprint);
    }

    /**
     * @throws Exception
     */
    public function removeAttribute(ModelBlueprint $modelBlueprint, AttributeBlueprint $attributeBlueprint): RemoveColumnAction
    {
        return new RemoveColumnAction($attributeBlueprint);
    }

    /**
     * @throws Exception
     */
    public function renameAttribute(ModelBlueprint $modelBlueprint, AttributeBlueprint $oldAttributeBlueprint, AttributeBlueprint $newAttributeBlueprint): RenameColumnAction
    {
        return new RenameColumnAction($oldAttributeBlueprint, $newAttributeBlueprint);
    }

    /**
     * @throws Exception
     */
    public function updateAttribute(ModelBlueprint $modelBlueprint, AttributeBlueprint $oldAttributeBlueprint, AttributeBlueprint $newAttributeBlueprint): UpdateColumnAction
    {
        return new UpdateColumnAction($oldAttributeBlueprint, $newAttributeBlueprint);
    }
}
