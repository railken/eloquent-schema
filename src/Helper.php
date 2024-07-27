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

    public function getModelBlueprint($ini): ModelBlueprint
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

    public function createModel(ModelBlueprint $model): ResultResolver
    {
        return $this->callResolver('createModel', $model);
    }

    public function removeModel(string|Model $ini): ResultResolver
    {
        $model = $this->getModelBlueprint($ini);

        return $this->callResolver('removeModel', $model);
    }

    public function updateModel(string|Model $ini, ModelBlueprint $newModelBlueprint): ResultResolver
    {
        $oldModelBlueprint = $this->getModelBlueprint($ini);
        $this->callResolver('fillBlueprintFromCurrentStatus', $oldModelBlueprint);
        $newModelBlueprint->instance($oldModelBlueprint->instance);

        return $this->callResolver('updateModel', $oldModelBlueprint, $newModelBlueprint);
    }

    public function createAttribute(string|Model $ini, AttributeBlueprint $attribute): ResultResolver
    {
        $modelBlueprint = $this->getModelBlueprint($ini);
        $attribute->model($modelBlueprint);
        $this->callResolver('fillBlueprintFromCurrentStatus', $modelBlueprint);

        return $this->callResolver('createAttribute', $modelBlueprint, $attribute);
    }

    public function removeAttribute(string|Model $ini, string $attributeName): ResultResolver
    {
        $modelBlueprint = $this->getModelBlueprint($ini);
        $this->callResolver('fillBlueprintFromCurrentStatus', $modelBlueprint);

        $attributeBlueprint = $modelBlueprint->getAttributeByName($attributeName);

        return $this->callResolver('removeAttribute', $modelBlueprint, $attributeBlueprint);
    }

    public function renameAttribute(string|Model $ini, string $oldAttributeName, string $newAttributeName): ResultResolver
    {
        $modelBlueprint = $this->getModelBlueprint($ini);
        $this->callResolver('fillBlueprintFromCurrentStatus', $modelBlueprint);

        $oldAttributeBlueprint = $modelBlueprint->getAttributeByName($oldAttributeName);
        $newAttributeBlueprint = clone $oldAttributeBlueprint;
        $newAttributeBlueprint->name($newAttributeName);

        return $this->callResolver('renameAttribute', $modelBlueprint, $oldAttributeBlueprint, $newAttributeBlueprint);
    }

    public function updateAttribute(string|Model $ini, string $oldAttributeName, AttributeBlueprint $newAttributeBlueprint): ResultResolver
    {
        $modelBlueprint = $this->getModelBlueprint($ini);
        $this->callResolver('fillBlueprintFromCurrentStatus', $modelBlueprint);

        $oldAttributeBlueprint = $modelBlueprint->getAttributeByName($oldAttributeName);
        $newAttributeBlueprint->model($modelBlueprint);

        return $this->callResolver('updateAttribute', $modelBlueprint, $oldAttributeBlueprint, $newAttributeBlueprint);
    }
}
