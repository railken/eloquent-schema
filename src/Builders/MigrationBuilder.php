<?php

namespace Railken\EloquentSchema\Builders;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Railken\EloquentSchema\Actions\Eloquent\Attribute;
use Railken\EloquentSchema\Actions\Migration\CreateColumnAction;
use Railken\EloquentSchema\Actions\Migration\CreateTableAction;
use Railken\EloquentSchema\Actions\Migration\RemoveColumnAction;
use Railken\EloquentSchema\Actions\Migration\RemoveTableAction;
use Railken\EloquentSchema\Actions\Migration\RenameColumnAction;
use Railken\EloquentSchema\Actions\Migration\UpdateColumnAction;
use Railken\EloquentSchema\Actions\Migration\UpdateTableAction;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Blueprints\ModelBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;
use Railken\EloquentSchema\Support;

class MigrationBuilder extends Builder
{
    protected AttributeBlueprint $attribute;

    public function createModel(ModelBlueprint $modelBlueprint): CreateTableAction
    {
        return new CreateTableAction($modelBlueprint);
    }

    public function removeModel(string|Model $ini): RemoveTableAction
    {
        $modelBlueprint = $this->getBlueprint($ini);

        return new RemoveTableAction($modelBlueprint);
    }

    public function updateModel(string|Model $ini, ModelBlueprint $newModelBlueprint): UpdateTableAction
    {
        $model = $this->getModel($ini);
        $oldModelBlueprint = $this->getBlueprint($ini);

        $this->schemaRetriever->getAttributesBlueprint($oldModelBlueprint);
        $classEditor = new ClassEditor(Support::getPathByObject($oldModelBlueprint->instance));
        foreach ($oldModelBlueprint->attributes as $attribute) {
            Attribute::callHooks('set', [$classEditor, $attribute]); // Refactor this hook
        }

        $newModelBlueprint->instance($model);
        $oldModelBlueprint->instance($model);

        return new UpdateTableAction($oldModelBlueprint, $newModelBlueprint);
    }

    public function createAttribute(string|Model $ini, AttributeBlueprint $attribute): CreateColumnAction
    {
        $modelBlueprint = $this->getBlueprint($ini);

        $attribute->model($modelBlueprint);

        return new CreateColumnAction($attribute);
    }

    /**
     * @throws Exception
     */
    public function removeAttribute(string|Model $ini, string $attributeName): RemoveColumnAction
    {
        $modelBlueprint = $this->getBlueprint($ini);

        $attribute = $this->schemaRetriever->getAttributeBlueprint($modelBlueprint->table, $attributeName);
        $attribute->model($modelBlueprint);

        return new RemoveColumnAction($attribute);
    }

    /**
     * @throws Exception
     */
    public function renameAttribute(string|Model $ini, string $oldAttributeName, string $newAttributeName): RenameColumnAction
    {
        $modelBlueprint = $this->getBlueprint($ini);

        $oldAttribute = $this->schemaRetriever->getAttributeBlueprint($modelBlueprint->table, $oldAttributeName);
        $oldAttribute->model($modelBlueprint);

        $newAttribute = clone $oldAttribute;
        $newAttribute->name($newAttributeName);

        return new RenameColumnAction($oldAttribute, $newAttribute);
    }

    /**
     * @throws Exception
     */
    public function updateAttribute(string|Model $ini, string $oldAttributeName, AttributeBlueprint $newAttribute): UpdateColumnAction
    {
        $modelBlueprint = $this->getBlueprint($ini);

        $oldAttribute = $this->schemaRetriever->getAttributeBlueprint($modelBlueprint->table, $oldAttributeName);
        $oldAttribute->model($modelBlueprint);
        $newAttribute->model($modelBlueprint);

        return new UpdateColumnAction($oldAttribute, $newAttribute);
    }
}
