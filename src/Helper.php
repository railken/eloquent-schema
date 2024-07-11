<?php

namespace Railken\EloquentSchema;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Migrations\Migrator;

use Closure;
use Railken\EloquentSchema\Concerns\Schema\DatabaseSchemaRetriever;
use Railken\EloquentSchema\Concerns\Schema\SchemaRetrieverInterface;

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

    public function setSchemaRetriever(SchemaRetrieverInterface $schemaRetriever)
    {
        $this->schemaRetriever = $schemaRetriever;
    }

    public function getSchemaRetriever(): SchemaRetrieverInterface
    {
        return $this->schemaRetriever;
    }

    public function addModelFolders(array $folders)
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

    public function createAttribute(string $table, Closure $closure)
    {
        return [
            $this->getModelBuilder()->createAttribute($table, $closure)->save(),
            $this->getMigrationBuilder()->createAttribute($table, $closure)->save()
        ];
    }
}
