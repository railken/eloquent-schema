<?php

namespace Tests;

use Illuminate\Support\ServiceProvider;

class SchemaServiceProvider extends ServiceProvider
{
    /**
     * @inherit
     */
    public function register()
    {
        $this->loadMigrationsFrom(__DIR__ . '/Generated/migrations');
        $this->loadMigrationsFrom(__DIR__ . '/../vendor/orchestra/testbench-core/laravel/database/migrations');
    }
}
