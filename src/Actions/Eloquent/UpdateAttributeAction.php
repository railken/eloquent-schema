<?php

namespace Railken\EloquentSchema\Actions\Eloquent;

use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

class UpdateAttributeAction extends Attribute
{
    protected AttributeBlueprint $oldAttribute;
    protected AttributeBlueprint $newAttribute;

    public function __construct(ClassEditor $classEditor, AttributeBlueprint $oldAttribute, AttributeBlueprint $newAttribute)
    {
        $this->oldAttribute = $oldAttribute;
        $this->newAttribute = $newAttribute;

        parent::__construct($classEditor);
    }

    public function run(): void
    {
        $this->removeFromModel($this->oldAttribute);
        $this->addToModel($this->newAttribute);
        $this->save();
    }
}
