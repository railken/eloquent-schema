<?php

namespace Railken\EloquentSchema\Actions\Eloquent;

use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

class CreateAttributeAction extends AttributeAction
{
    protected AttributeBlueprint $newAttribute;

    public function __construct(ClassEditor $classEditor, AttributeBlueprint $newAttribute)
    {
        $this->newAttribute = $newAttribute;

        parent::__construct($classEditor);
    }

    public function run(): void
    {
        $this->addToModel($this->newAttribute);
        $this->save();
    }
}
