<?php

namespace Railken\EloquentSchema\Builders;

use Railken\EloquentSchema\Editors\ClassEditor;
use Railken\EloquentSchema\Schema\SchemaRetrieverInterface;
use Illuminate\Database\Eloquent\Model;

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

    protected function initializeByTable(string $table): void
    {
        $this->table = $table;
        $model = $this->newModelInstanceByTable($table);
        $this->model = $model;

        $reflector = new \ReflectionClass(get_class($model));
        $path = $reflector->getFileName();

        $this->classEditor = new ClassEditor($path);
    }

}
