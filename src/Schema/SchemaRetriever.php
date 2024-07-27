<?php

namespace Railken\EloquentSchema\Schema;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use KitLoong\MigrationsGenerator\Enum\Driver;
use KitLoong\MigrationsGenerator\Schema\MySQLSchema;
use KitLoong\MigrationsGenerator\Schema\PgSQLSchema;
use KitLoong\MigrationsGenerator\Schema\Schema;
use KitLoong\MigrationsGenerator\Schema\SQLiteSchema;
use KitLoong\MigrationsGenerator\Schema\SQLSrvSchema;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Exceptions\AttributeMissingFromDatabaseException;

use function WyriHaximus\listInstantiatableClassesInDirectory;

class SchemaRetriever implements SchemaRetrieverInterface
{
    protected static array $attributes = [];

    protected Collection $models;

    protected Collection $folders;

    public function __construct()
    {
        $this->models = new Collection;
        $this->folders = new Collection;
    }

    public static function addAttributes(array $attributes): void
    {
        self::$attributes = array_merge(self::$attributes, $attributes);
    }

    public function addModelFolders(array $folders)
    {
        // @todo, parse files/directory only first time they are requested
        foreach ($folders as $folder) {
            foreach (listInstantiatableClassesInDirectory($folder) as $class) {
                $model = new $class;

                if ($model instanceof Model) {

                    if ($this->models->has($model->getTable())) {
                        throw new \Exception(sprintf('Model already added %s: %s', $class, $model->getTable()));
                    }

                    $this->models->put($model->getTable(), $class);

                }
            }
            $this->folders = $this->folders->merge($folders);
        }
    }

    public function getFolders(): Collection
    {
        return $this->folders;
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

        if (! $driver) {
            throw new Exception('Failed to find database driver.');
        }

        return match ($driver) {
            Driver::MARIADB->value, Driver::MYSQL->value => app(MySQLSchema::class),
            Driver::PGSQL->value => app(PgSQLSchema::class),
            Driver::SQLITE->value => app(SQLiteSchema::class),
            Driver::SQLSRV->value => app(SQLSrvSchema::class),
            default => throw new Exception('The database driver in use is not supported.'),
        };
    }

    /**
     * Get DB schema by the database connection name.
     *
     * @throws \Exception
     */
    public function getMigrationGeneratorSchemaByName(string $table): \KitLoong\MigrationsGenerator\Schema\Models\Table
    {
        return $this->getMigrationGeneratorSchema()->getTable($table);
    }

    public function newBlueprintByColumn($column, $params): AttributeBlueprint
    {
        return $this->guessType($column, $params);
    }

    public function guessType($column, $params): AttributeBlueprint
    {
        $guesses = new Collection;

        foreach (self::$attributes as $type) {
            if ($type::isMe($column, $params)) {
                $guesses->push($type);
            }
        }

        $class = $guesses->last();

        if (empty($class)) {
            throw new AttributeMissingFromDatabaseException($column->getName(), $column->getType()->value);
        }

        return $class::make($column->getName());
    }
}
