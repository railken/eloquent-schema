<?php

namespace Railken\EloquentSchema\Blueprints;

class AttributeDiffBlueprint
{
    public AttributeBlueprint $oldAttribute;

    public AttributeBlueprint $newAttribute;

    public function __construct(AttributeBlueprint $oldAttribute, AttributeBlueprint $newAttribute)
    {
        $this->oldAttribute = $oldAttribute;
        $this->newAttribute = $newAttribute;
    }
}
