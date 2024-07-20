<?php

namespace Railken\EloquentSchema\Actions\Eloquent;

use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

class CreateAttributeAction extends Attribute
{
    protected AttributeBlueprint $attribute;
    public function __construct(ClassEditor $classEditor, AttributeBlueprint $attribute)
    {
        $this->attribute = $attribute;

        parent::__construct($classEditor);
    }

    public function run(): void
    {
        $this->addToModel($this->attribute);
        $this->save();
    }
}
