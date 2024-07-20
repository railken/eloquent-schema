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
    public ?bool $fillable = null;
    public ?bool $required = null;

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

    public function migrateDrop(): string
    {
        return "->dropColumn('{$this->name}')";
    }

    public function migrateChange(): string
    {
        return "->change()";
    }

    public function migrateNullable(): string
    {
        return "->nullable()";
    }

    public function migrateColumn(): string
    {
        return "->{$this->db}('{$this->name}')";
    }

    public function migrate(ActionCase $action): string
    {
        $migration = "\$table";

        if (in_array($action, [ActionCase::Remove])) {
            $migration .= $this->migrateDrop();
        }

        if (in_array($action, [ActionCase::Create, ActionCase::Update])) {
            $migration .= $this->migrateColumn();

            if ($this->required === false) {
                $migration .= $this->migrateNullable();
            }
        }

        if (in_array($action, [ActionCase::Update])) {
            $migration .= $this->migrateChange();
        }

        return $migration.";";
    }




}
