<?php

namespace Railken\EloquentSchema;

use Railken\Lem\Contracts\EntityContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Railken\EloquentSchema\Concerns\Schema\SchemaRetrieverInterface;

class Resolver
{
    public static $classes = [
        'model' => ModelBuilder::class,
        'migration' => MigrationBuilder::class
    ];

    public function resolveByModel(SchemaRetrieverInterface $schemaRetriever, string $builder)
    {
        $builders = self::$classes;

        if (!isset($builders[$builder])) {
            throw new \Exception(sprintf("No builder %s found for scope %s. Class: %s", $builder, $scope, $path));
        }

        return new $builders[$builder]($schemaRetriever);
    }

}
