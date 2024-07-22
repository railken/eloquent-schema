<?php

namespace Railken\EloquentSchema;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Schema\DatabaseSchemaRetriever;
use Railken\EloquentSchema\Schema\SchemaRetrieverInterface;

class Helper
{
    protected Collection $migrations;

    protected Resolver $resolver;

    protected SchemaRetrieverInterface $schemaRetriever;

    /**
     * Create a new migration rollback command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->resolver = new Resolver();
        $this->migrations = Collection::make();
        $this->setSchemaRetriever(new DatabaseSchemaRetriever());
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

    public function getModelBuilder()
    {
        return $this->resolver->resolveByModel($this->schemaRetriever, 'model');
    }

    public function getMigrationBuilder()
    {
        return $this->resolver->resolveByModel($this->schemaRetriever, 'migration');
    }

    public function createAttribute(string|Model $ini, AttributeBlueprint $attribute): array
    {
        return [
            'model' => $this->getModelBuilder()->createAttribute($ini, $attribute),
            'migration' => $this->getMigrationBuilder()->createAttribute($ini, $attribute),
        ];
    }

    public function removeAttribute(string|Model $ini, string $attributeName): array
    {
        return [
            'model' => $this->getModelBuilder()->removeAttribute($ini, $attributeName),
            'migration' => $this->getMigrationBuilder()->removeAttribute($ini, $attributeName),
        ];
    }

    public function renameAttribute(string|Model $ini, string $oldAttributeName, string $newAttributeName): array
    {
        return [
            'model' => $this->getModelBuilder()->renameAttribute($ini, $oldAttributeName, $newAttributeName),
            'migration' => $this->getMigrationBuilder()->renameAttribute($ini, $oldAttributeName, $newAttributeName),
        ];
    }

    public function updateAttribute(string|Model $ini, string $attributeName, AttributeBlueprint $newAttribute): array
    {
        return [
            'model' => $this->getModelBuilder()->updateAttribute($ini, $attributeName, $newAttribute),
            'migration' => $this->getMigrationBuilder()->updateAttribute($ini, $attributeName, $newAttribute),
        ];
    }
}
