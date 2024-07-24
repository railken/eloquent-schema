<?php

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Railken\EloquentSchema\Blueprints\Attributes\StringAttribute;
use Railken\EloquentSchema\Blueprints\ModelBlueprint;
use Railken\EloquentSchema\Builders\MigrationBuilder;
use Railken\EloquentSchema\Builders\ModelBuilder;

#[RunTestsInSeparateProcesses]
class ModelPrimaryKeyTest extends \Tests\BaseCase
{
    public function test_primary_key_create_and_update()
    {
        $result = $this->getService()->createModel(ModelBlueprint::make('duck')->attributes([
            StringAttribute::make('name')->fillable(true),
        ])->anonymous(true)->primaryKey(['name']))->run();

        $final = <<<'EOD'
        <?php
        
        use Illuminate\Database\Eloquent\Model;
        return new class extends Model
        {
            protected $casts = [
                'name' => 'string',
            ];
            protected $fillable = [
                'name',
            ];
            protected $table = 'duck';
            
            protected $primaryKey = 'name';
            
            public $timestamps = false;
        };
        EOD;

        $this->assertEquals($final, $result->get(ModelBuilder::class)->first());

        $final = <<<'EOD'
        Schema::create('duck', function (Blueprint $table) {
            $table->string('name');
            $table->primary('name');
        });
        EOD;

        $this->assertEquals($final, $result->get(MigrationBuilder::class)->first()->get('up'));

        $final = <<<'EOD'
        Schema::dropTable('duck');
        EOD;
        $this->assertEquals($final, $result->get(MigrationBuilder::class)->first()->get('down'));

        $this->artisan('migrate');

        $model = $this->newModel('Duck');

        $model->create([
            'name' => 'Quack',
        ]);

        $this->assertEquals('Quack', $model::where('name', 'Quack')->first()->name);

    }
}
