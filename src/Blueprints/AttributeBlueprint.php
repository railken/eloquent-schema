<?php

namespace Railken\EloquentSchema\Blueprints;

use Exception;
use KitLoong\MigrationsGenerator\Enum\Migrations\Method\IndexType;
use Railken\EloquentSchema\ActionCase;
use Railken\EloquentSchema\Editors\ClassEditor;

class AttributeBlueprint
{
    public string $name;
    public string $type;
    public string $db;
    public bool $fillable = true;
    public bool $required = true;

    public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(string $name): AttributeBlueprint
    {
        return new static($name);
    }

    public function name(string $name): AttributeBlueprint
    {
        $this->name = $name;

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
        $this->required($nullable);

        return $this;
    }
}
