<?php

namespace Tests;

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Railken\EloquentSchema\Blueprints\Attributes\StringAttribute;
use Railken\EloquentSchema\Builders\MigrationBuilder;
use Railken\EloquentSchema\Builders\ModelBuilder;

#[RunTestsInSeparateProcesses]
class UpdateAttributeTest extends BaseCase
{
    public function test_create_and_update()
    {
        $model = $this->newModel();

        $result = $this->getService()->createAttribute(
            $model,
            StringAttribute::make('color')->fillable(true)
        )->run();

        $this->artisan('migrate');

        $result = $this->getService()->updateAttribute(
            $model,
            'color',
            StringAttribute::make('color')->fillable(false)->default('red')
        )->run();

        $final = <<<'EOD'
        <?php
        
        use Illuminate\Database\Eloquent\Model;
        
        return new class extends Model
        {
            protected $guarded = [
                'color',
            ];
            protected $table = 'parrots';
        
            protected $fillable = [
                'name',
            ];

            protected $casts = [
                'name' => 'string',
                'color' => 'string',
            ];
        };
        
        EOD;

        $this->assertEquals($final, $result->get(ModelBuilder::class)->first());

        $final = <<<'EOD'
        Schema::table('parrots', function (Blueprint $table) {
            $table->string('color')->default('red')->change();
        });
        EOD;
        $this->assertEquals($final, $result->get(MigrationBuilder::class)->first()->get('up'));

        $final = <<<'EOD'
        Schema::table('parrots', function (Blueprint $table) {
            $table->string('color')->default(null)->change();
        });
        EOD;
        $this->assertEquals($final, $result->get(MigrationBuilder::class)->first()->get('down'));

        $this->artisan('migrate');
    }
}
