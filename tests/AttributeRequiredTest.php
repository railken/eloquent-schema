<?php

namespace Tests;

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Railken\EloquentSchema\Blueprints\Attributes\StringAttribute;
use Railken\EloquentSchema\Builders\MigrationBuilder;
use Railken\EloquentSchema\Builders\ModelBuilder;

#[RunTestsInSeparateProcesses]
class AttributeRequiredTest extends BaseCase
{
    public function test_required()
    {
        $model = $this->newModel();

        $result = $this->getService()->createAttribute(
            $model,
            StringAttribute::make('code')->required(true)
        )->run();

        $final = <<<'EOD'
        <?php
        
        use Illuminate\Database\Eloquent\Model;
        
        return new class extends Model
        {
            protected $table = 'parrots';
        
            protected $fillable = [
                'name',
            ];
        
            protected $casts = [
                'name' => 'string',
                'code' => 'string',
            ];
        };
        
        EOD;

        $this->assertEquals($final, $result->get(ModelBuilder::class)->first());

        $final = <<<'EOD'
        Schema::table('parrots', function (Blueprint $table) {
            $table->string('code');
        });
        EOD;
        $this->assertEquals($final, $result->get(MigrationBuilder::class)->first()->get('up'));

        $final = <<<'EOD'
        Schema::table('parrots', function (Blueprint $table) {
            $table->dropColumn('code');
        });
        EOD;
        $this->assertEquals($final, $result->get(MigrationBuilder::class)->first()->get('down'));

        $this->artisan('migrate');

        $model = $this->newModel();
        $model = $model->fill([
            'name' => 'test',
        ]);
        $model->code = '123';
        $model->save();

        $this->assertEquals('123', $this->newModel()->where('id', 1)->first()->code);
        $this->assertEquals(true, $this->getService()->getModelBlueprint($this->newModel())->getAttributeByName('code')->required);
    }

    public function test_not_fillable()
    {
        $model = $this->newModel();

        $result = $this->getService()->createAttribute(
            $model,
            StringAttribute::make('code')->required(false)
        )->run();

        $final = <<<'EOD'
        <?php
        
        use Illuminate\Database\Eloquent\Model;
        
        return new class extends Model
        {
            protected $table = 'parrots';
        
            protected $fillable = [
                'name',
            ];
        
            protected $casts = [
                'name' => 'string',
                'code' => 'string',
            ];
        };
        
        EOD;

        $this->assertEquals($final, $result->get(ModelBuilder::class)->first());

        $final = <<<'EOD'
        Schema::table('parrots', function (Blueprint $table) {
            $table->string('code')->nullable();
        });
        EOD;
        $this->assertEquals($final, $result->get(MigrationBuilder::class)->first()->get('up'));

        $final = <<<'EOD'
        Schema::table('parrots', function (Blueprint $table) {
            $table->dropColumn('code');
        });
        EOD;
        $this->assertEquals($final, $result->get(MigrationBuilder::class)->first()->get('down'));

        $this->artisan('migrate');

        $model = $this->newModel();
        $model = $model->fill([
            'name' => 'test',
        ]);
        $model->code = '123';
        $model->save();

        $this->assertEquals('123', $this->newModel()->where('id', 1)->first()->code);
        $this->assertEquals(false, $this->getService()->getModelBlueprint($this->newModel())->getAttributeByName('code')->required);

    }
}
