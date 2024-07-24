<?php

namespace Railken\EloquentSchema\Blueprints;

class AttributeBlueprint
{
    public string $name;

    public string $type;

    public ?string $cast = null;

    public string $db;

    public ?bool $fillable = null;

    public ?bool $required = null;

    public mixed $default = null;

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

    public function cast(string $cast): AttributeBlueprint
    {
        $this->cast = $cast;

        return $this;
    }

    public function model(ModelBlueprint $model): AttributeBlueprint
    {
        $this->model = $model;

        return $this;
    }

    public function fillable(?bool $fillable = true): AttributeBlueprint
    {
        $this->fillable = $fillable;

        return $this;
    }

    public function required(?bool $required = true): AttributeBlueprint
    {
        $this->required = $required;

        return $this;
    }

    public function nullable(?bool $nullable = true): AttributeBlueprint
    {
        $this->required(! $nullable);

        return $this;
    }

    public function default(mixed $default): AttributeBlueprint
    {
        $this->default = $default;

        return $this;
    }
}
