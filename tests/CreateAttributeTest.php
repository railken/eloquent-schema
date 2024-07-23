<?php

namespace Tests;

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Railken\EloquentSchema\Blueprints\Attributes\StringAttribute;
use Railken\EloquentSchema\Builders\MigrationBuilder;
use Railken\EloquentSchema\Builders\ModelBuilder;

#[RunTestsInSeparateProcesses]
class CreateAttributeTest extends BaseCase
{
    public function test_create_attribute()
    {
        $model = $this->newModel();

        $result = $this->getService()->createAttribute(
            $model,
            StringAttribute::make('description')->fillable(true)
        )->run();

        $final = <<<'EOD'
        <?php
        
        use Illuminate\Database\Eloquent\Model;
        
        return new class extends Model
        {
            protected $table = 'parrots';
        
            protected $fillable = [
                'name',
                'description',
            ];
        
            protected $casts = [
                'name' => 'string',
                'description' => 'string',
            ];
        };
        
        EOD;

        $this->assertEquals($final, $result->get(ModelBuilder::class)->first());

        $final = <<<'EOD'
        Schema::table('parrots', function (Blueprint $table) {
            $table->string('description');
        });
        EOD;
        $this->assertEquals($final, $result->get(MigrationBuilder::class)->first()->get('up'));

        $final = <<<'EOD'
        Schema::table('parrots', function (Blueprint $table) {
            $table->dropColumn('description');
        });
        EOD;
        $this->assertEquals($final, $result->get(MigrationBuilder::class)->first()->get('down'));

        $this->artisan('migrate');

        $this->assertEquals('A very nice parrot', $this->newModel()->create([
            'description' => 'A very nice parrot',
        ])->description);
    }
}
