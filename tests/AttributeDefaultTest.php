<?php

namespace Tests;

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Railken\EloquentSchema\Blueprints\Attributes\IntegerAttribute;
use Railken\EloquentSchema\Builders\MigrationBuilder;
use Railken\EloquentSchema\Builders\ModelBuilder;

#[RunTestsInSeparateProcesses]
class AttributeDefaultTest extends BaseCase
{
    public function test_default()
    {
        $result = $this->getService()->createAttribute(
            $this->newModel(),
            IntegerAttribute::make('weight')->fillable(true)->default(5)
        )->run();

        $class = <<<'EOD'
        <?php
        
        use Illuminate\Database\Eloquent\Model;
        
        return new class extends Model
        {
            protected $table = 'parrots';
        
            protected $fillable = [
                'name',
                'weight',
            ];
        
            protected $casts = [
                'name' => 'string',
                'weight' => 'integer',
            ];
        };
        
        EOD;

        $up = <<<'EOD'
        Schema::table('parrots', function (Blueprint $table) {
            $table->integer('weight')->default('5');
        });
        EOD;

        $down = <<<'EOD'
        Schema::table('parrots', function (Blueprint $table) {
            $table->dropColumn('weight');
        });
        EOD;

        $this->assertEquals($class, $result->get(ModelBuilder::class)->first());
        $this->assertEquals($up, $result->get(MigrationBuilder::class)->first()->get('up'));
        $this->assertEquals($down, $result->get(MigrationBuilder::class)->first()->get('down'));
        $this->artisan('migrate');

        $this->assertEquals(null, $this->newModel()->create([])->weight); // default updates only db
        $this->assertEquals(5, $this->newModel()->where('id', 1)->first()->weight);

    }

    public function test_default_migrations()
    {
        $result = $this->getService()->createAttribute(
            $this->newModel(),
            IntegerAttribute::make('weight')->fillable(true)->default(5)
        )->run();

        $up = <<<'EOD'
        Schema::table('parrots', function (Blueprint $table) {
            $table->integer('weight')->default('5');
        });
        EOD;

        $down = <<<'EOD'
        Schema::table('parrots', function (Blueprint $table) {
            $table->dropColumn('weight');
        });
        EOD;

        $this->assertEquals($up, $result->get(MigrationBuilder::class)->first()->get('up'));
        $this->assertEquals($down, $result->get(MigrationBuilder::class)->first()->get('down'));
        $this->artisan('migrate');

        $result = $this->getService()->updateAttribute(
            $this->newModel(),
            'weight',
            IntegerAttribute::make('weight')->default(3)
        )->run();

        $up = <<<'EOD'
        Schema::table('parrots', function (Blueprint $table) {
            $table->integer('weight')->default('3')->change();
        });
        EOD;

        $down = <<<'EOD'
        Schema::table('parrots', function (Blueprint $table) {
            $table->integer('weight')->default('5')->change();
        });
        EOD;

        $this->assertEquals($up, $result->get(MigrationBuilder::class)->first()->get('up'));
        $this->assertEquals($down, $result->get(MigrationBuilder::class)->first()->get('down'));
        $this->artisan('migrate');

    }
}
