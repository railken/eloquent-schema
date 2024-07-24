<?php

namespace Railken\EloquentSchema\Builders;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Railken\EloquentSchema\Actions\Eloquent\Attribute;
use Railken\EloquentSchema\Actions\Eloquent\CreateAttributeAction;
use Railken\EloquentSchema\Actions\Eloquent\CreateModelAction;
use Railken\EloquentSchema\Actions\Eloquent\RemoveAttributeAction;
use Railken\EloquentSchema\Actions\Eloquent\RemoveModelAction;
use Railken\EloquentSchema\Actions\Eloquent\RenameAttributeAction;
use Railken\EloquentSchema\Actions\Eloquent\UpdateAttributeAction;
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

    public function removeModel(string|Model $ini): RemoveModelAction
    {
        $model = $this->getModel($ini);
        $modelBlueprint = $this->newModelBlueprintByModel($model);

        return new RemoveModelAction($modelBlueprint);
    }

    /**
     * Add a new attribute to the table and the relative model
     *
     * @throws Exception
     */
    public function createAttribute(string|Model $ini, AttributeBlueprint $attributeBlueprint): CreateAttributeAction
    {
        $modelBlueprint = $this->getBlueprint($ini);
        $attributeBlueprint->model($modelBlueprint);

        $classEditor = new ClassEditor(Support::getPathByObject($attributeBlueprint->model->instance));

        return new CreateAttributeAction($classEditor, $attributeBlueprint);
    }

    /**
     * Remove an attribute from the table and the relative model
     *
     * @throws Exception
     */
    public function removeAttribute(string|Model $ini, string $attributeName): RemoveAttributeAction
    {
        $modelBlueprint = $this->getBlueprint($ini);

        $attributeBlueprint = $this->schemaRetriever->getAttributeBlueprint($modelBlueprint->table, $attributeName);
        $attributeBlueprint->model($modelBlueprint);

        $classEditor = new ClassEditor(Support::getPathByObject($attributeBlueprint->model->instance));

        return new RemoveAttributeAction($classEditor, $attributeBlueprint);
    }

    /**
     * Rename an attribute
     *
     * @throws Exception
     */
    public function renameAttribute(string|Model $ini, string $oldAttributeName, string $newAttributeName): RenameAttributeAction
    {
        $modelBlueprint = $this->getBlueprint($ini);

        $oldAttributeBlueprint = $this->schemaRetriever->getAttributeBlueprint($modelBlueprint->table, $oldAttributeName);
        $oldAttributeBlueprint->model($modelBlueprint);

        $classEditor = new ClassEditor(Support::getPathByObject($modelBlueprint->instance));
        Attribute::callHooks('set', [$classEditor, $oldAttributeBlueprint]); // Refactor this hook

        $newAttributeBlueprint = clone $oldAttributeBlueprint;
        $newAttributeBlueprint->name($newAttributeName);

        return new RenameAttributeAction($classEditor, $oldAttributeBlueprint, $newAttributeBlueprint);
    }

    /**
     * update an attribute
     *
     * @throws Exception
     */
    public function updateAttribute(string|Model $ini, string $attributeName, AttributeBlueprint $newAttributeBlueprint): UpdateAttributeAction
    {
        $modelBlueprint = $this->getBlueprint($ini);

        $oldAttributeBlueprint = $this->schemaRetriever->getAttributeBlueprint($modelBlueprint->table, $attributeName);

        $classEditor = new ClassEditor(Support::getPathByObject($modelBlueprint->instance));
        Attribute::callHooks('set', [$classEditor, $oldAttributeBlueprint]); // Refactor this hook

        $oldAttributeBlueprint->model($modelBlueprint);
        $newAttributeBlueprint->model($modelBlueprint);

        return new UpdateAttributeAction($classEditor, $oldAttributeBlueprint, $newAttributeBlueprint);
    }
}
