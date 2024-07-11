<?php

namespace Railken\EloquentSchema\Concerns\Schema;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use function WyriHaximus\listInstantiatableClassesInDirectory;
use function WyriHaximus\listClassesInDirectories;

use KitLoong\MigrationsGenerator\Schema\MySQLSchema;
use KitLoong\MigrationsGenerator\Schema\PgSQLSchema;
use KitLoong\MigrationsGenerator\Schema\Schema;
use KitLoong\MigrationsGenerator\Schema\SQLiteSchema;
use KitLoong\MigrationsGenerator\Schema\SQLSrvSchema;
use KitLoong\MigrationsGenerator\Enum\Driver;

class SchemaRetriever implements SchemaRetrieverInterface
{
    protected Collection $models;

    public function __construct()
    {
        $this->models = new Collection();
    }

    public function addModelFolders(array $folders)
    {
        // @todo, parse files/directory only first time they are requested
        foreach ($folders as $folder) {
            foreach (listInstantiatableClassesInDirectory($folder) as $class) {
                $model = new $class();

                if ($model instanceof Model) {

                    if ($this->models->has($model->getTable())) {
                        throw new \Exception(sprintf("Model already added %s: %s", $class, $model->getTable()));
                    }

                    $this->models->put($model->getTable(), $class);

                }
            }
        }
    }

    public function retrieveAttributes(string $table): Collection
    {
        return Collection::make(DB::getSchemaBuilder()->getColumns($table));
    }

    public function getModels(): Collection
    {
        return $this->models;
    }

    /**
     * Get DB schema by the database connection name.
     *
     * @throws \Exception
     */
    public function getMigrationGeneratorSchema(): Schema
    {
        $driver = DB::getDriverName();

        if (!$driver) {
            throw new Exception('Failed to find database driver.');
        }

        return match ($driver) {
            Driver::MARIADB->value, Driver::MYSQL->value => $this->schema = app(MySQLSchema::class),
            Driver::PGSQL->value => $this->schema                         = app(PgSQLSchema::class),
            Driver::SQLITE->value => $this->schema                        = app(SQLiteSchema::class),
            Driver::SQLSRV->value => $this->schema                        = app(SQLSrvSchema::class),
            default => throw new Exception('The database driver in use is not supported.'),
        };
    }

    /**
     * Get DB schema by the database connection name.
     *
     * @throws \Exception
     */
    public function getMigrationGeneratorSchemaByName(string $table)
    {
        return $this->getMigrationGeneratorSchema()->getTable($table);
    }
}
