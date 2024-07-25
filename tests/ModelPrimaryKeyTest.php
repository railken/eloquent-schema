<?php

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Railken\EloquentSchema\Blueprints\Attributes\StringAttribute;
use Railken\EloquentSchema\Blueprints\Attributes\TextAttribute;
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
        ])->anonymous(true)->incrementing(false)->primaryKey(['name']))->run();

        $final = <<<'EOD'
        <?php
        
        use Illuminate\Database\Eloquent\Model;
        return new class extends Model
        {
            protected $table = 'duck';
            
            protected $primaryKey = 'name';
            
            public $incrementing = false;
            
            public $timestamps = false;
            
            protected $fillable = [
                'name',
            ];
            
            protected $casts = [
                'name' => 'string',
            ];
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

        $result = $this->getService()->updateModel($model,
            ModelBlueprint::make('duck')->attributes([
                StringAttribute::make('species')->fillable(true),
                StringAttribute::make('name')->fillable(true),
                TextAttribute::make('description')->fillable(true)->required(false),
            ])->incrementing(false)->anonymous(true)->primaryKey(['species', 'name'])
        )->run();

        $final = <<<'EOD'
        <?php
        
        use Illuminate\Database\Eloquent\Model;
        return new class extends Model
        {
            protected $table = 'duck';
            
            protected $primaryKey = [
                'species',
                'name',
            ];
            
            public $incrementing = false;
            
            public $timestamps = false;
            
            protected $fillable = [
                'species',
                'description',
                'name',
            ];
            
            protected $casts = [
                'species' => 'string',
                'description' => 'string',
                'name' => 'string',
            ];
        };
        EOD;

        $this->assertEquals($final, $result->get(ModelBuilder::class)->first());

        $final = <<<'EOD'
        Schema::table('duck', function (Blueprint $table) {
            $table->string('species');
            $table->text('description')->nullable();
            $table->dropPrimary();
            $table->primary(['species','name']);
        });
        EOD;
        //@fix default(null)
        $this->assertEquals($final, $result->get(MigrationBuilder::class)->first()->get('up'));

        $final = <<<'EOD'
        Schema::table('duck', function (Blueprint $table) {
            $table->dropColumn('species');
            $table->dropColumn('description');
            $table->dropPrimary();
            $table->primary('name');
        });
        EOD;
        $this->assertEquals($final, $result->get(MigrationBuilder::class)->first()->get('down'));

        $this->artisan('migrate');

        $model = $this->newModel('Duck');

        $model->create([
            'name' => 'Good Quack',
            'species' => 'A good species',
            'description' => 'The best Duck',
        ]);

        $this->assertEquals([
            'name' => 'Good Quack',
            'species' => 'A good species',
            'description' => 'The best Duck',
        ], $model::where('name', 'Good Quack')->first()->toArray());

    }
}
