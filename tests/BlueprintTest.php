<?php

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Railken\EloquentSchema\Blueprints\Attributes;

#[RunTestsInSeparateProcesses]
class BlueprintTest extends \Tests\BaseCase
{
    public function test_model_blueprint()
    {
        $model = $this->newModel();

        $modelBlueprint = $this->getService()->getModelBlueprint($model);

        $this->assertInstanceOf(Attributes\IdAttribute::class, $modelBlueprint->getAttributeByName('id'));
        $this->assertInstanceOf(Attributes\StringAttribute::class, $modelBlueprint->getAttributeByName('name'));
        $this->assertInstanceOf(Attributes\CreatedAtAttribute::class, $modelBlueprint->getAttributeByName('created_at'));
        $this->assertInstanceOf(Attributes\UpdatedAtAttribute::class, $modelBlueprint->getAttributeByName('updated_at'));
    }
}
