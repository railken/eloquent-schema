<?php

namespace Railken\EloquentSchema\Schema;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use KitLoong\MigrationsGenerator\Enum\Driver;
use KitLoong\MigrationsGenerator\Enum\Migrations\Method\IndexType;
use KitLoong\MigrationsGenerator\Schema\MySQLSchema;
use KitLoong\MigrationsGenerator\Schema\PgSQLSchema;
use KitLoong\MigrationsGenerator\Schema\Schema;
use KitLoong\MigrationsGenerator\Schema\SQLiteSchema;
use KitLoong\MigrationsGenerator\Schema\SQLSrvSchema;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Blueprints\Attributes\IdAttribute;
use Railken\EloquentSchema\Blueprints\Attributes\StringAttribute;
use Railken\EloquentSchema\Blueprints\Attributes\TextAttribute;

use function WyriHaximus\listInstantiatableClassesInDirectory;

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
                        throw new \Exception(sprintf('Model already added %s: %s', $class, $model->getTable()));
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

    public function getAttributeBlueprint(string $table, string $attributeName): AttributeBlueprint
    {
        $params = $this->getMigrationGeneratorSchema()->getTable($table);

        $column = $params->getColumns()->filter(function ($column) use ($attributeName) {
            return $column->getName() == $attributeName;
        })->first();

        if (empty($column)) {
            throw new Exception(sprintf("Couldn't find the attribute in the db %s", $attributeName));
        }

        $indexes = $params->getIndexes()->filter(function ($index) use ($attributeName) {
            return in_array($attributeName, $index->getColumns()) && count($index->getColumns()) > 1;
        });

        if (count($indexes) > 0) {
            throw new Exception('Please change your index before removing the attribute');
        }

        $attribute = $this->guessType($column, $params);
        $attribute->required($column->isNotNull());
        $attribute->default($column->getDefault());

        return $attribute;
    }

    public function guessType($column, $params): AttributeBlueprint
    {
        if ($column->getName() == 'id') {

            // Check that field is has an index primary
            $id = $params->getIndexes()->filter(function ($index) {
                return in_array('id', $index->getColumns()) && count($index->getColumns()) == 1 && $index->getType() == IndexType::PRIMARY;
            })->first();

            if (! empty($id)) {
                return IdAttribute::make($column->getName());
            }
        }

        // Handle all types...
        return match ($column->getType()->value) {
            'text' => TextAttribute::make($column->getName()),
            default => StringAttribute::make($column->getName()),
        };
    }
}
