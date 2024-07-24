<?php

namespace Railken\EloquentSchema;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Blueprints\ModelBlueprint;
use Railken\EloquentSchema\Builders\Builder;
use Railken\EloquentSchema\Schema\DatabaseSchemaRetriever;
use Railken\EloquentSchema\Schema\SchemaRetrieverInterface;

class Helper
{
    protected Collection $migrations;

    protected array $resolvers = [];

    protected SchemaRetrieverInterface $schemaRetriever;

    /**
     * Create a new migration rollback command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->migrations = Collection::make();
        $this->setSchemaRetriever(new DatabaseSchemaRetriever);
    }

    public function setSchemaRetriever(SchemaRetrieverInterface $schemaRetriever): void
    {
        $this->schemaRetriever = $schemaRetriever;
    }

    public function getSchemaRetriever(): SchemaRetrieverInterface
    {
        return $this->schemaRetriever;
    }

    public function addModelFolders(array $folders): void
    {
        $this->schemaRetriever->addModelFolders($folders);
    }

    public function getModels(): Collection
    {
        return $this->schemaRetriever->getModels();
    }

    public function addMigrationFolders(array $folders)
    {
        /*$files = $this->migrator->getMigrationFiles($this->migrator->paths());

        foreach ($files as $path) {

            $migration = require $path;

            if ($migration instanceof Migration) {

                /*$results = $this
                    ->retrieveAttributesByTableName($path)
                ;


                $this->schema->put($model->getTable())->push($path);

            }
        }*/
    }

    public function addResolvers(array $resolvers): void
    {
        $this->setResolvers(array_merge($this->resolvers, $resolvers));
    }

    public function setResolvers(array $resolvers): void
    {
        $this->resolvers = $resolvers;
    }

    public function callResolver($method): ResultResolver
    {
        $result = new ResultResolver;

        foreach ($this->resolvers as $resolverClass) {
            if (is_subclass_of($resolverClass, Builder::class)) {
                $resolver = new $resolverClass($this->schemaRetriever);

                $args = func_get_args();
                unset($args[0]);
                $result[$resolverClass] = $resolver->$method(...$args);
            }
        }

        return $result;
    }

    public function createModel(ModelBlueprint $model): ResultResolver
    {
        return $this->callResolver('createModel', $model);
    }

    public function removeModel(string|Model $ini): ResultResolver
    {
        return $this->callResolver('removeModel', $ini);
    }

    public function createAttribute(string|Model $ini, AttributeBlueprint $attribute): ResultResolver
    {
        return $this->callResolver('createAttribute', $ini, $attribute);
    }

    public function removeAttribute(string|Model $ini, string $attributeName): ResultResolver
    {
        return $this->callResolver('removeAttribute', $ini, $attributeName);
    }

    public function renameAttribute(string|Model $ini, string $oldAttributeName, string $newAttributeName): ResultResolver
    {
        return $this->callResolver('renameAttribute', $ini, $oldAttributeName, $newAttributeName);
    }

    public function updateAttribute(string|Model $ini, string $oldAttributeName, AttributeBlueprint $newAttribute): ResultResolver
    {
        return $this->callResolver('updateAttribute', $ini, $oldAttributeName, $newAttribute);
    }
}
