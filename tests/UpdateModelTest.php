<?php

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Railken\EloquentSchema\Blueprints\ModelBlueprint;
use Railken\EloquentSchema\Builders\MigrationBuilder;
use Railken\EloquentSchema\Builders\ModelBuilder;

#[RunTestsInSeparateProcesses]
class UpdateModelTest extends \Tests\BaseCase
{
    public function test_remove_model_simple()
    {
        $result = $this->getService()->updateModel(
            $this->newModel(),
            ModelBlueprint::make()
                ->primary(anotherId)
                ->incrementing(false)
                ->timestamps(false)
        )->run();

        $this->assertEquals(null, $result->get(ModelBuilder::class)->first());

        $final = <<<'EOD'
        Schema::dropTable('parrots');
        EOD;
        $this->assertEquals($final, $result->get(MigrationBuilder::class)->first()->get('up'));

        $final = <<<'EOD'
        Schema::createTable('parrots', function (Blueprint $table) {
             $table->id();
             $table->timestamps()
        });
        EOD;

        $this->expectException(\ErrorException::class);
        $this->newModel();

    }
}
