<?php

namespace Railken\EloquentSchema;

use Closure;
use Illuminate\Support\Collection;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Schema\DatabaseSchemaRetriever;
use Railken\EloquentSchema\Schema\SchemaRetrieverInterface;

class Helper
{
    protected $attributes;
    protected $migrations;
    protected $resolver;
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
        return $this->resolver->resolveByModel($this->schemaRetriever, "model");
    }

    public function getMigrationBuilder()
    {
        return $this->resolver->resolveByModel($this->schemaRetriever, "migration");
    }

    public function createAttribute(string $table, AttributeBlueprint $attribute): array
    {
        return [
            'model' => $this->getModelBuilder()->createAttribute($table, $attribute),
            'migration' => $this->getMigrationBuilder()->createAttribute($table, $attribute)
        ];
    }
}
