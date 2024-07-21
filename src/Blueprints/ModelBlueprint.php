<?php

namespace Railken\EloquentSchema\Blueprints;

use Illuminate\Support\Collection;
use Railken\EloquentSchema\ActionCase;

class ModelBlueprint
{
    public string $table;

    public Collection $attributes;

    public ActionCase $action;

    public function __construct(ActionCase $action)
    {
        $this->action($action);
    }

    public function action(ActionCase $action): ModelBlueprint
    {
        $this->action = $action;

        return $this;
    }

    public function table(string $table): ModelBlueprint
    {
        $this->table = $table;

        return $this;
    }

    public function addAttribute(AttributeBlueprint $attribute): ModelBlueprint
    {
        $this->attributes[$attribute->name] = $attribute;

        return $this;
    }
}
