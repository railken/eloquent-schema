<?php

namespace Railken\EloquentSchema\Builders;

use Illuminate\Database\Eloquent\Model;
use Railken\EloquentSchema\Actions\Action;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Blueprints\ModelBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;
use Railken\EloquentSchema\Schema\SchemaRetrieverInterface;

abstract class Builder
{
    protected Model $model;

    protected string $table;

    protected ClassEditor $classEditor;

    protected SchemaRetrieverInterface $schemaRetriever;

    public function __construct(SchemaRetrieverInterface $schemaRetriever)
    {
        $this->schemaRetriever = $schemaRetriever;
    }

    public function newModelInstanceByTable(string $table)
    {
        $class = $this->schemaRetriever->getModels()->get($table);

        return new $class;
    }

    protected function getModel(string|Model $ini): Model
    {
        if ($ini instanceof Model) {
            return $ini;
        }

        if (is_subclass_of($ini, Model::class)) {
            return new $ini;
        }

        return $this->newModelInstanceByTable($ini);
    }

    public function getBlueprint($ini): ModelBlueprint
    {
        $model = $this->getModel($ini);

        return $this->newModelBlueprintByModel($model);
    }

    public function newModelBlueprintByModel(Model $model): ModelBlueprint
    {
        $reflection = new \ReflectionClass($model);

        $blueprint = new ModelBlueprint($reflection->getName());
        $blueprint->namespace($reflection->getNamespaceName());
        $blueprint->table($model->getTable());
        $blueprint->instance($model);

        return $blueprint;
    }

    abstract public function createModel(
        ModelBlueprint $modelBlueprint
    ): Action;

    abstract public function removeModel(
        string $ini
    ): Action;

    abstract public function createAttribute(
        string|Model $ini,
        AttributeBlueprint $attribute
    ): Action;

    abstract public function removeAttribute(
        string|Model $ini,
        string $attributeName
    ): Action;

    abstract public function renameAttribute(
        string|Model $ini,
        string $oldAttributeName,
        string $newAttributeName
    ): Action;

    abstract public function updateAttribute(
        string|Model $ini,
        string $oldAttributeName,
        AttributeBlueprint $newAttribute
    ): Action;
}
