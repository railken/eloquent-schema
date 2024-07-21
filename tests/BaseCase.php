<?php

namespace Tests;

abstract class BaseCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    public function getService()
    {
        return app('eloquent.schema');
    }

    protected function getPackageProviders($app)
    {
        return [
            \Railken\EloquentSchema\Providers\EloquentSchemaServiceProvider::class,
            \Tests\SchemaServiceProvider::class,
        ];
    }
}
