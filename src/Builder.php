<?php

namespace Railken\EloquentSchema;

use Illuminate\Database\Eloquent\Model;
use ReflectionClass;
use Illuminate\Support\Collection;
use PhpParser\PrettyPrinter;
use PhpParser\NodeFinder;
use PhpParser\NodeDumper;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use Railken\Template\Generators;
use Railken\EloquentSchema\Concerns\Schema\SchemaRetrieverInterface;

class Builder
{
    protected $model;
    protected $table;
    protected $classEditor;
    protected $operation = "update";
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

    protected function initializeByTable(string $table)
    {
        $this->table = $table;
        $model = $this->newModelInstanceByTable($table);

        $this->model = $model;

        $reflector = new \ReflectionClass(get_class($model));
        $path = $reflector->getFileName();

        $this->classEditor = new ClassEditor($path);
    }

    public function save()
    {
        $this->getClassEditor()->save();

        return $this;
    }

    public function render()
    {
        return $this->getClassEditor()->render();
    }
}
