<?php

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Railken\EloquentSchema\Blueprints\Attributes\StringAttribute;
use Railken\EloquentSchema\Blueprints\ModelBlueprint;
use Railken\EloquentSchema\Builders\MigrationBuilder;
use Railken\EloquentSchema\Builders\ModelBuilder;
use Railken\EloquentSchema\Exceptions\ClassAlreadyExistsException;

#[RunTestsInSeparateProcesses]
class CreateModelTest extends \Tests\BaseCase
{
    public function test_create_model_simple()
    {
        $result = $this->getService()->createModel(ModelBlueprint::make('cat'))->run();

        $final = <<<'EOD'
        <?php
        
        use Illuminate\Database\Eloquent\Model;
        
        class Cat extends Model
        {
            protected $table = 'cat';
        }
        EOD;

        $this->assertEquals($final, $result->get(ModelBuilder::class)->first());

        $final = <<<'EOD'
        Schema::create('cat', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        EOD;

        $this->assertEquals($final, $result->get(MigrationBuilder::class)->first()->get('up'));

        $final = <<<'EOD'
        Schema::dropTable('cat');
        EOD;
        $this->assertEquals($final, $result->get(MigrationBuilder::class)->first()->get('down'));

        $this->artisan('migrate');
    }

    public function test_create_model_with_namespace()
    {
        $result = $this->getService()->createModel(
            ModelBlueprint::make('cat')
                ->namespace('App\\Models')
                ->workingDir(__DIR__.'/Generated')
        )->run();

        $final = <<<'EOD'
        <?php
        
        namespace App\Models;
        
        use Illuminate\Database\Eloquent\Model;
        
        class Cat extends Model
        {
            protected $table = 'cat';
        }
        EOD;

        $this->assertEquals(
            __DIR__.'/Generated/App/Models/Cat.php',
            $result->get(ModelBuilder::class)->keys()->first()
        );

        $this->assertEquals($final, $result->get(ModelBuilder::class)->first());
    }

    public function test_create_model_anonymous()
    {
        $result = $this->getService()->createModel(
            ModelBlueprint::make('cat')->anonymous(true)
        )->run();

        $final = <<<'EOD'
        <?php
        
        use Illuminate\Database\Eloquent\Model;
        return new class extends Model
        {
            protected $table = 'cat';
        };
        EOD;

        $this->assertEquals($final, $result->get(ModelBuilder::class)->first());

        $this->artisan('migrate');

        $model = $this->newModel('Cat');
        $model->create([]);

        $this->assertEquals(1, $model->where('id', 1)->first()->id);
    }

    public function test_create_already_exists_exception()
    {
        $this->expectException(ClassAlreadyExistsException::class);

        $result = $this->getService()->createModel(
            ModelBlueprint::make('cat')
                ->namespace('App\\Models')
                ->workingDir(__DIR__.'/Generated')
        )->run();

        $result = $this->getService()->createModel(
            ModelBlueprint::make('cat')
                ->namespace('App\\Models')
                ->workingDir(__DIR__.'/Generated')
        )->run();
    }

    public function test_create_model_custom_id()
    {
        $result = $this->getService()->createModel(ModelBlueprint::make('duck')->attributes([
            StringAttribute::make('name'),
            StringAttribute::make('color'),
        ])->primaryKey(['name', 'color'])->incrementing(false))->run();

        $final = <<<'EOD'
        <?php
        
        use Illuminate\Database\Eloquent\Model;
        
        class Duck extends Model
        {
            protected $table = 'duck';
            
            protected $incrementing = false;
            
            protected $primaryKey = ['name', 'color'];
        }
        EOD;

        $this->assertEquals($final, $result->get(ModelBuilder::class)->first());

        $final = <<<'EOD'
        Schema::create('duck', function (Blueprint $table) {
            $table->string('name');
            $table->string('color');
            $table->primary(['name', 'color']);
        });
        EOD;

        $this->assertEquals($final, $result->get(MigrationBuilder::class)->first()->get('up'));

        $final = <<<'EOD'
        Schema::dropTable('duck');
        EOD;
        $this->assertEquals($final, $result->get(MigrationBuilder::class)->first()->get('down'));

        $this->artisan('migrate');
    }
}
