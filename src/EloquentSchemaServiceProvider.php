<?php

namespace Railken\EloquentSchema;

use Illuminate\Support\ServiceProvider;
use Railken\EloquentSchema\Console\Commands\Mapper;
use Illuminate\Support\Facades\Event;

class EloquentSchemaServiceProvider extends ServiceProvider
{
    /**
     * @inherit
     */
    public function register()
    {
        $this->app->register(\Archetype\ServiceProvider::class);
        $this->app->register(\Railken\Template\TemplateServiceProvider::class);
        $this->app->register(\KitLoong\MigrationsGenerator\MigrationsGeneratorServiceProvider::class);

        $this->app->singleton('eloquent.schema', Helper::class);

        parent::register();
    }

    public function boot()
    {
    }
}
