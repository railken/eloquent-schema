<?php

namespace Tests;

use Railken\EloquentSchema\Blueprints\Attributes\StringAttribute;
use Railken\EloquentSchema\Builders\MigrationBuilder;
use Railken\EloquentSchema\Builders\ModelBuilder;

class AttributeFillableTest extends BaseCase
{
    public function test_fillable()
    {
        $model = $this->newModel();

        $result = $this->getService()->createAttribute(
            $model,
            StringAttribute::make('fillable')->fillable(true)
        )->run();

        $final = <<<'EOD'
        <?php
        
        use Illuminate\Database\Eloquent\Model;
        
        return new class extends Model
        {
            protected $table = 'parrots';
        
            protected $fillable = [
                'name',
                'fillable',
            ];
        
            protected $casts = [
                'name' => 'string',
                'fillable' => 'string',
            ];
        };
        EOD;

        $this->assertEquals($final, $result->get(ModelBuilder::class)->first());

        $final = <<<'EOD'
        Schema::table('parrots', function (Blueprint $table) {
            $table->string('fillable');
        });
        EOD;
        $this->assertEquals($final, $result->get(MigrationBuilder::class)->first()->get('up'));

        $final = <<<'EOD'
        Schema::table('parrots', function (Blueprint $table) {
            $table->dropColumn('fillable');
        });
        EOD;
        $this->assertEquals($final, $result->get(MigrationBuilder::class)->first()->get('down'));

        $this->artisan('migrate');

        $this->assertEquals('chip', $this->newModel()->create([
            'fillable' => 'chip',
        ])->fillable);
    }

    public function test_not_fillable()
    {
        $model = $this->newModel();

        $result = $this->getService()->createAttribute(
            $model,
            StringAttribute::make('not_fillable')->fillable(false)
        )->run();

        $final = <<<'EOD'
        <?php
        
        use Illuminate\Database\Eloquent\Model;
        
        return new class extends Model
        {
            protected $guarded = [
                'not_fillable',
            ];
            protected $table = 'parrots';
        
            protected $fillable = [
                'name',
            ];
        
            protected $casts = [
                'name' => 'string',
                'not_fillable' => 'string',
            ];
        };
        EOD;

        $this->assertEquals($final, $result->get(ModelBuilder::class)->first());

        $final = <<<'EOD'
        Schema::table('parrots', function (Blueprint $table) {
            $table->string('not_fillable');
        });
        EOD;
        $this->assertEquals($final, $result->get(MigrationBuilder::class)->first()->get('up'));

        $final = <<<'EOD'
        Schema::table('parrots', function (Blueprint $table) {
            $table->dropColumn('not_fillable');
        });
        EOD;
        $this->assertEquals($final, $result->get(MigrationBuilder::class)->first()->get('down'));

        $model = $this->newModel();

        $this->artisan('migrate');

        try {
            $this->assertEquals('silent', $this->newModel()->create([
                'not_fillable' => 'chop',
            ])->not_fillable);
        } catch (\Exception $e) {
            $this->assertEquals(\Illuminate\Database\QueryException::class, get_class($e));
        }

        $model = $this->newModel();
        $model->not_fillable = 'chop';
        $model->save();

        $this->assertEquals('chop', $model->not_fillable);
    }
}
