<?php

namespace Railken\EloquentSchema\Blueprints;

class AttributeBlueprint
{
    public string $name;

    public string $type;

    public static string $migration;

    public ?ModelBlueprint $model = null;

    public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(): AttributeBlueprint
    {
        // @phpstan-ignore-next-line
        return new static(...func_get_args());
    }

    public function name(string $name): AttributeBlueprint
    {
        $this->name = $name;

        return $this;
    }

    public function model(ModelBlueprint $model): AttributeBlueprint
    {
        $this->model = $model;

        return $this;
    }

    public function __call($name, $arguments): AttributeBlueprint
    {
        $this->$name = $arguments[0];

        return $this;
    }

    public function __get($name)
    {
        /** @noinspection PhpExpressionAlwaysNullInspection */
        return $this->$name ?? null;
    }
}
