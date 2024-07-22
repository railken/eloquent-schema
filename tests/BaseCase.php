<?php

namespace Tests;

use Illuminate\Support\Facades\File;
use Railken\EloquentSchema\Actions\Eloquent\Attribute;
use Railken\EloquentSchema\Builders\MigrationBuilder;
use Railken\EloquentSchema\Builders\ModelBuilder;
use Railken\EloquentSchema\Hooks\CastHook;
use Railken\EloquentSchema\Hooks\FillableHook;
use Railken\EloquentSchema\Hooks\GuardedHook;

abstract class BaseCase extends \Orchestra\Testbench\TestCase
{
    public function getService()
    {
        return app('eloquent.schema');
    }

    /**
     * Call this template method before each test method is run.
     */
    public function setUp(): void
    {
        parent::setUp();

        Attribute::setHooks([
            FillableHook::class,
            GuardedHook::class,
            CastHook::class,
        ]);

        $this->getService()->setResolvers([
            ModelBuilder::class,
            MigrationBuilder::class,
        ]);

        File::deleteDirectory(__DIR__.'/Generated');
        File::copyDirectory(__DIR__.'/Source', __DIR__.'/Generated');
        File::cleanDirectory(__DIR__.'/../vendor/orchestra/testbench-core/laravel/database/migrations');

        $this->artisan('migrate:fresh');

        $this->getService()->addModelFolders([
            __DIR__.'/Generated',
        ]);

        $this->getService()->addMigrationFolders([
            __DIR__.'/Generated/migrations',
        ]);

    }

    public function newModel(string $name = 'Parrot')
    {
        return require __DIR__."/Generated/$name.php";
    }

    protected function getPackageProviders($app)
    {
        return [
            \Railken\EloquentSchema\Providers\EloquentSchemaServiceProvider::class,
            \Tests\SchemaServiceProvider::class,
        ];
    }
}
