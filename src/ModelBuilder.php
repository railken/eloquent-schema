<?php

namespace Railken\EloquentSchema;

use Illuminate\Database\Eloquent\Model;
use Archetype\Facades\PHPFile;
use ReflectionClass;
use Illuminate\Support\Collection;
use Closure;

class ModelBuilder extends Builder
{
    protected string $path;
    protected \Archetype\PHPFile $file;

    protected function initializeByTable(string $table)
    {
        parent::initializeByTable($table);

        $reflector = new \ReflectionClass(get_class($this->model));
        $path = $reflector->getFileName();

        $this->path = $path;
        $this->file = PHPFile::load($path);
    }

    public function createAttribute(string $table, Closure $closure)
    {
        $this->initializeByTable($table);

        $attribute = new AttributeBlueprint(ActionCase::Create);
        $closure($attribute);

        $file = $this->file;

        $attribute->convert()->modelUp($file);

        // Required only with validation

        return $this;
    }

    public function removeAttribute(string $table, string $attributeName)
    {
        $this->initializeByTable($table);

        $attribute = new AttributeBlueprint(ActionCase::Remove);
        $attribute->name($attributeName);
        $attribute->fillFromDatabaseSchema($this->schemaRetriever->getMigrationGeneratorSchemaByName($table));

        $file = $this->file;
        $attribute->convert()->modelDown($file);

        return $this;
    }
}
