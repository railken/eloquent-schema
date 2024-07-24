<?php

namespace Railken\EloquentSchema\Builders;

use Illuminate\Database\Eloquent\Model;
use Railken\EloquentSchema\Actions\Action;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Blueprints\ModelBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;
use Railken\EloquentSchema\Schema\SchemaRetrieverInterface;
use Railken\EloquentSchema\Support;

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

    protected function getClassEditor(): ClassEditor
    {
        return $this->classEditor;
    }

    protected function initialize(string|Model $ini): Builder
    {
        if ($ini instanceof Model) {
            return $this->initializeByModel($ini);
        }

        return $this->initializeByTable($ini);
    }

    protected function initializeByTable(string $table): Builder
    {
        $this->table = $table;
        $model = $this->newModelInstanceByTable($table);
        $this->model = $model;

        $this->classEditor = new ClassEditor(Support::getPathByObject($model));

        return $this;
    }

    protected function initializeByModel(Model $model): Builder
    {
        $this->table = $model->getTable();
        $this->model = $model;

        $this->classEditor = new ClassEditor(Support::getPathByObject($model));

        return $this;
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
