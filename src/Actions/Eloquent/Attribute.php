<?php

namespace Railken\EloquentSchema\Actions\Eloquent;

use Railken\EloquentSchema\Actions\Action;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

abstract class Attribute extends Action
{
    protected ClassEditor $classEditor;
    protected array $result = [];

    public function __construct(ClassEditor $classEditor)
    {
        $this->classEditor = $classEditor;
    }

    public function getResult(): array
    {
        return $this->result;
    }

    public function save(): void
    {
        $this->result = $this->classEditor->save();
    }

    public function addToFillable(AttributeBlueprint $attribute): void
    {
        if ($attribute->fillable) {
            $this->classEditor->addProtectedAttribute('fillable', $attribute->name);
        }
    }
    public function addToGuarded(AttributeBlueprint $attribute): void
    {
        if (!$attribute->fillable) {
            $this->classEditor->addProtectedAttribute('guarded', $attribute->name);
        }
    }
    public function addToCasts(AttributeBlueprint $attribute): void
    {
        $this->classEditor->addProtectedAttribute('casts', [$attribute->name => $attribute->type]);
    }

    public function removeFromFillable(AttributeBlueprint $attribute): void
    {
        $this->classEditor->removeAttributeValue('fillable', $attribute->name);
    }

    public function removeFromGuarded(AttributeBlueprint $attribute): void
    {
        $this->classEditor->removeAttributeValue('guarded', $attribute->name);
    }

    public function removeFromCasts(AttributeBlueprint $attribute): void
    {
        $this->classEditor->removeAttributeValueByIndex('casts', $attribute->name);
    }

    public function addToModel(AttributeBlueprint $attribute): void
    {
        $this->addToFillable($attribute);
        $this->addToGuarded($attribute);
        $this->addToCasts($attribute);
    }

    public function removeFromModel(AttributeBlueprint $attribute): void
    {
        $this->removeFromFillable($attribute);
        $this->removeFromGuarded($attribute);
        $this->removeFromCasts($attribute);
    }
}
