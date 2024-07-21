<?php

namespace Railken\EloquentSchema\Providers;

use Illuminate\Support\ServiceProvider;
use Railken\EloquentSchema\Helper;

class EloquentSchemaServiceProvider extends ServiceProvider
{
    /**
     * @inherit
     */
    public function register(): void
    {
        $this->app->register(\Archetype\ServiceProvider::class);
        $this->app->register(\Railken\Template\TemplateServiceProvider::class);
        $this->app->register(\KitLoong\MigrationsGenerator\MigrationsGeneratorServiceProvider::class);

        $this->app->singleton('eloquent.schema', Helper::class);

        parent::register();
    }

}
