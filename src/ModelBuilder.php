<?php

namespace Railken\EloquentSchema;

use Illuminate\Database\Eloquent\Model;
use Archetype\Facades\PHPFile;
use ReflectionClass;
use Illuminate\Support\Collection;
use Closure;

class ModelBuilder extends Builder
{
    protected function initializeByTable(string $table): void
    {
        parent::initializeByTable($table);

        $reflector = new \ReflectionClass(get_class($this->model));
        $path = $reflector->getFileName();

        $this->classEditor = new ClassEditor($path);
    }

    public function createAttribute(string $table, Closure $closure): ModelBuilder
    {
        $this->initializeByTable($table);

        $attribute = new AttributeBlueprint(ActionCase::Create);
        $closure($attribute);


        $attribute->convert()->modelUp($this->classEditor);

        // Required only with validation

        return $this;
    }

    public function removeAttribute(string $table, string $attributeName): ModelBuilder
    {
        $this->initializeByTable($table);

        $attribute = new AttributeBlueprint(ActionCase::Remove);
        $attribute->name($attributeName);
        $attribute->fillFromDatabaseSchema($this->schemaRetriever->getMigrationGeneratorSchemaByName($table));

        $attribute->convert()->modelDown($this->classEditor);

        return $this;
    }
}
