<?php

namespace Railken\EloquentSchema\Actions\Eloquent;

use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;
use Railken\EloquentSchema\Support;

class CreateAttributeAction extends Attribute
{
    protected AttributeBlueprint $attribute;

    public function __construct(AttributeBlueprint $attribute)
    {
        $this->attribute = $attribute;

        parent::__construct(new ClassEditor(Support::getPathByObject($attribute->model)));
    }

    public function run(): void
    {
        $this->addToModel($this->attribute);
        $this->save();
    }
}
