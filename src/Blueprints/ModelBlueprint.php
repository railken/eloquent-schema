<?php

namespace Railken\EloquentSchema\Blueprints;

use Illuminate\Support\Str;

class ModelBlueprint
{
    public string $name;

    public string $table;

    public string $class;

    public string $workingDir;

    public bool $anonymous = false;

    public string $namespace = '';

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->table($name);
        $this->class($name);
    }

    public function namespace(string $namespace): ModelBlueprint
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function anonymous(bool $anonymous): ModelBlueprint
    {
        $this->anonymous = $anonymous;

        return $this;
    }

    public function workingDir(string $workingDir): ModelBlueprint
    {
        if (substr($workingDir, -1) !== DIRECTORY_SEPARATOR) {
            $workingDir .= DIRECTORY_SEPARATOR;
        }

        $this->workingDir = $workingDir;

        return $this;
    }

    public function updateNameSpaceToWorkingDir(): ModelBlueprint
    {

        $this->workingDir($this->workingDir.str_replace('\\', '/', $this->namespace));

        return $this;
    }

    public static function make(): ModelBlueprint
    {
        // @phpstan-ignore-next-line
        return new static(...func_get_args());
    }

    public function table(string $table): ModelBlueprint
    {
        $this->table = strtolower(Str::snake($table));

        return $this;
    }

    public function class(string $class): ModelBlueprint
    {
        $this->class = ucfirst(Str::camel($class));

        return $this;
    }
}
