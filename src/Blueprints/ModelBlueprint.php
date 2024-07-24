<?php

namespace Railken\EloquentSchema\Blueprints;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Railken\EloquentSchema\Exceptions\AttributeNotFoundException;

class ModelBlueprint
{
    public string $name;

    public string $table;

    public string $class;

    /** @var AttributeBlueprint[] */
    public array $attributes = [];

    public string $workingDir;

    public array $primaryKey = ['id'];

    public bool $incrementing = true;

    public bool $anonymous = false;

    public string $namespace = '';

    public Model $instance;

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

    public function instance(Model $instance): ModelBlueprint
    {
        $this->instance = $instance;

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

    public function attributes(array $attributes): ModelBlueprint
    {
        $this->attributes = [];

        foreach ($attributes as $attribute) {
            if (! $attribute instanceof AttributeBlueprint) {
                throw new \Exception(sprintf('AttributeBlueprint should be used'));
            }

            $attribute->model($this);
            $this->attributes[$attribute->name] = $attribute;
        }

        return $this;
    }

    public function getAttributeByName(string $name): ?AttributeBlueprint
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->name == $name) {
                return $attribute;
            }
        }

        return null;
    }

    /**
     * @throws AttributeNotFoundException
     */
    public function primaryKey(array $primaryKey): ModelBlueprint
    {
        foreach ($primaryKey as $key) {
            $attribute = $this->getAttributeByName($key);

            if (! $attribute) {
                throw new AttributeNotFoundException($key, $this->name);
            }
        }

        $this->primaryKey = $primaryKey;

        return $this;
    }

    public function incrementing(bool $incrementing): ModelBlueprint
    {
        $this->incrementing = $incrementing;

        return $this;
    }

    public function hasAttributes(array $needle): bool
    {
        return count(array_intersect(array_keys($this->attributes), $needle)) == count($needle);
    }
}
