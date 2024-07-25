<?php

namespace Railken\EloquentSchema\Actions\Eloquent;

use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

class UpdateAttributeAction extends CreateAttributeAction
{
    protected AttributeBlueprint $oldAttribute;

    public function __construct(ClassEditor $classEditor, AttributeBlueprint $oldNewAttribute, AttributeBlueprint $newAttribute)
    {
        $this->oldAttribute = $oldNewAttribute;

        parent::__construct($classEditor, $newAttribute);
    }

    public function run(): void
    {
        $this->removeFromModel($this->oldAttribute);
        $this->addToModel($this->newAttribute);
        $this->save();
    }
}
