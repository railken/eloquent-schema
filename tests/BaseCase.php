<?php

namespace Tests;

use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Railken\EloquentSchema\Actions\Eloquent\AttributeAction;
use Railken\EloquentSchema\Actions\Eloquent\ModelAction;
use Railken\EloquentSchema\Actions\Migration\ColumnAction;
use Railken\EloquentSchema\Blueprints\Attributes\CreatedAtAttribute;
use Railken\EloquentSchema\Blueprints\Attributes\IdAttribute;
use Railken\EloquentSchema\Blueprints\Attributes\IntegerAttribute;
use Railken\EloquentSchema\Blueprints\Attributes\StringAttribute;
use Railken\EloquentSchema\Blueprints\Attributes\TextAttribute;
use Railken\EloquentSchema\Blueprints\Attributes\TimestampAttribute;
use Railken\EloquentSchema\Blueprints\Attributes\UpdatedAtAttribute;
use Railken\EloquentSchema\Builders\MigrationBuilder;
use Railken\EloquentSchema\Builders\ModelBuilder;
use Railken\EloquentSchema\Hooks\CastHook;
use Railken\EloquentSchema\Hooks\DefaultHook;
use Railken\EloquentSchema\Hooks\FillableHook;
use Railken\EloquentSchema\Hooks\GuardedHook;
use Railken\EloquentSchema\Hooks\IncrementingHook;
use Railken\EloquentSchema\Hooks\PrimaryKeyHook;
use Railken\EloquentSchema\Hooks\RequiredHook;
use Railken\EloquentSchema\Hooks\TableHook;
use Railken\EloquentSchema\Hooks\TimestampsHook;
use Railken\EloquentSchema\Schema\SchemaRetriever;

#[RunTestsInSeparateProcesses]
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

        SchemaRetriever::addAttributes([
            StringAttribute::class,
            TextAttribute::class,
            IntegerAttribute::class,
            TimestampAttribute::class,
            IdAttribute::class,
            CreatedAtAttribute::class,
            UpdatedAtAttribute::class,
        ]);

        ColumnAction::setHooks([
            DefaultHook::class,
            RequiredHook::class,
        ]);

        AttributeAction::setHooks([
            GuardedHook::class,
            CastHook::class,
            FillableHook::class,
        ]);

        ModelAction::setHooks([
            TimestampsHook::class,
            IncrementingHook::class,
            PrimaryKeyHook::class,
            TableHook::class,
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
