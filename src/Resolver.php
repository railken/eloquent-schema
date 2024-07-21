<?php

namespace Railken\EloquentSchema;

use Exception;
use Railken\EloquentSchema\Builders\MigrationBuilder;
use Railken\EloquentSchema\Builders\ModelBuilder;
use Railken\EloquentSchema\Schema\SchemaRetrieverInterface;

class Resolver
{
    public static array $classes = [
        'model' => ModelBuilder::class,
        'migration' => MigrationBuilder::class,
    ];

    /**
     * @throws Exception
     */
    public function resolveByModel(SchemaRetrieverInterface $schemaRetriever, string $builder)
    {
        $builders = self::$classes;

        if (! isset($builders[$builder])) {
            throw new Exception(sprintf('No builder found: %s', $builder));
        }

        return new $builders[$builder]($schemaRetriever);
    }
}
