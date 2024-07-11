<?php

namespace Railken\EloquentSchema;

use Illuminate\Database\Eloquent\Model;
use Archetype\Facades\PHPFile;
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

    protected function initializeByTable(string $table)
    {
        $this->table = $table;
        $model = $this->newModelInstanceByTable($table);

        $this->model = $model;

        $reflector = new \ReflectionClass(get_class($model));
        $path = $reflector->getFileName();

        $this->path = $path;
        $this->file = PHPFile::load($path);
    }

    public function save()
    {
        file_put_contents($this->path, $this->render());

        return $this;
    }

    public function render()
    {
        return $this->file->render();
    }
}
