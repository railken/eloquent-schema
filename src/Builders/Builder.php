<?php

namespace Railken\EloquentSchema\Builders;

use Illuminate\Database\Eloquent\Model;
use Railken\EloquentSchema\Editors\ClassEditor;
use Railken\EloquentSchema\Schema\SchemaRetrieverInterface;
use Railken\EloquentSchema\Support;

class Builder
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

        return new $class();
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
}
