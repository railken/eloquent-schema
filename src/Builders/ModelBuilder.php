<?php

namespace Railken\EloquentSchema\Builders;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Railken\EloquentSchema\Actions\Eloquent\AttributeAction;
use Railken\EloquentSchema\Actions\Eloquent\CreateAttributeAction;
use Railken\EloquentSchema\Actions\Eloquent\CreateModelAction;
use Railken\EloquentSchema\Actions\Eloquent\ModelAction;
use Railken\EloquentSchema\Actions\Eloquent\RemoveAttributeAction;
use Railken\EloquentSchema\Actions\Eloquent\RemoveModelAction;
use Railken\EloquentSchema\Actions\Eloquent\RenameAttributeAction;
use Railken\EloquentSchema\Actions\Eloquent\UpdateAttributeAction;
use Railken\EloquentSchema\Actions\Eloquent\UpdateModelAction;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Blueprints\ModelBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;
use Railken\EloquentSchema\Support;

class ModelBuilder extends Builder
{
    public function createModel(ModelBlueprint $modelBlueprint): CreateModelAction
    {
        if (empty($modelBlueprint->workingDir)) {
            $modelBlueprint->workingDir = $this->schemaRetriever->getFolders()->first();
        }
        $modelBlueprint->updateNameSpaceToWorkingDir();

        return new CreateModelAction($modelBlueprint);
    }

    public function removeModel(ModelBlueprint $modelBlueprint): RemoveModelAction
    {
        return new RemoveModelAction($modelBlueprint);
    }

    public function fillBlueprintFromCurrentStatus(ModelBlueprint $modelBlueprint)
    {
        $classEditor = new ClassEditor(Support::getPathByObject($modelBlueprint->instance));

        $params = $this->schemaRetriever->getMigrationGeneratorSchema()->getTable($modelBlueprint->table);

        $attributes = [];

        foreach ($params->getColumns() as $column) {
            $attribute = $this->schemaRetriever->newBlueprintByColumn($column, $params);
            AttributeAction::callHooks('set', [$classEditor, $attribute]); // Refactor this hook

            $attributes[] = $attribute;
        }

        $modelBlueprint->attributes($attributes);

        ModelAction::callHooks('updateBlueprintFromDatabase', [$modelBlueprint, $params]);
    }

    public function updateModel(ModelBlueprint $oldModelBlueprint, ModelBlueprint $newModelBlueprint): UpdateModelAction
    {
        return new UpdateModelAction($oldModelBlueprint, $newModelBlueprint);
    }

    /**
     * Add a new attribute to the table and the relative model
     *
     * @throws Exception
     */
    public function createAttribute(ModelBlueprint $modelBlueprint, AttributeBlueprint $attributeBlueprint): CreateAttributeAction
    {
        $classEditor = new ClassEditor(Support::getPathByObject($attributeBlueprint->model->instance));

        return new CreateAttributeAction($classEditor, $attributeBlueprint);
    }

    /**
     * Remove an attribute from the table and the relative model
     *
     * @throws Exception
     */
    public function removeAttribute(ModelBlueprint $modelBlueprint, AttributeBlueprint $attributeBlueprint): RemoveAttributeAction
    {
        $classEditor = new ClassEditor(Support::getPathByObject($attributeBlueprint->model->instance));

        return new RemoveAttributeAction($classEditor, $attributeBlueprint);
    }

    /**
     * Rename an attribute
     *
     * @throws Exception
     */
    public function renameAttribute(ModelBlueprint $modelBlueprint, AttributeBlueprint $oldAttributeBlueprint, AttributeBlueprint $newAttributeBlueprint): RenameAttributeAction
    {
        $classEditor = new ClassEditor(Support::getPathByObject($modelBlueprint->instance));

        return new RenameAttributeAction($classEditor, $oldAttributeBlueprint, $newAttributeBlueprint);
    }

    /**
     * update an attribute
     *
     * @throws Exception
     */
    public function updateAttribute(ModelBlueprint $modelBlueprint, AttributeBlueprint $oldAttributeBlueprint, AttributeBlueprint $newAttributeBlueprint): UpdateAttributeAction
    {
        $classEditor = new ClassEditor(Support::getPathByObject($modelBlueprint->instance));

        return new UpdateAttributeAction($classEditor, $oldAttributeBlueprint, $newAttributeBlueprint);
    }
}
