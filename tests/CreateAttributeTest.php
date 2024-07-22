<?php

namespace Tests;

use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Railken\EloquentSchema\Actions\Eloquent\Attribute;
use Railken\EloquentSchema\Blueprints\Attributes\StringAttribute;
use Railken\EloquentSchema\Hooks\CastHook;
use Railken\EloquentSchema\Hooks\FillableHook;
use Railken\EloquentSchema\Hooks\GuardedHook;

#[RunTestsInSeparateProcesses]
class CreateAttributeTest extends BaseCase
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
        Attribute::addHooks([
            FillableHook::class,
            GuardedHook::class,
            CastHook::class,
        ]);

        parent::setUp();

        if (! str_contains($this->name(), 'Generated')) {
            File::deleteDirectory(__DIR__.'/Generated');
            File::copyDirectory(__DIR__.'/Source', __DIR__.'/Generated');
            File::cleanDirectory(__DIR__.'/../vendor/orchestra/testbench-core/laravel/database/migrations');

            $this->artisan('migrate:fresh');
        }

        $this->getService()->addModelFolders([
            __DIR__.'/Generated',
        ]);

        $this->getService()->addMigrationFolders([
            __DIR__.'/Generated/migrations',
        ]);

        $this->artisan('migrate');

    }

    public function action(array $result): void
    {
        $result['model']->run();
        $result['migration']->run();
    }

    public function newModel()
    {
        return require __DIR__.'/Generated/Parrot.php';
    }

    public function test_fillable()
    {

        $model = $this->newModel();

        $model->fill([
            'fillable_field' => 'Fillable field',
        ]);

        $this->assertEquals([

        ], $model->toArray());

        $result = $this->getService()->createAttribute(
            $model,
            StringAttribute::make('fillable_field')->fillable(true)
        );

        $this->action($result);

        $model = $this->newModel();
        $model->fill([
            'fillable_field' => 'Fillable field',
        ]);

        $this->assertEquals([
            'fillable_field' => 'Fillable field',
        ], $model->toArray());
    }
}
