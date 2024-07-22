<?php

namespace Tests;

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Railken\EloquentSchema\Builders\MigrationBuilder;
use Railken\EloquentSchema\Builders\ModelBuilder;

#[RunTestsInSeparateProcesses]
class RemoveAttributeTest extends BaseCase
{
    public function test_remove()
    {
        $model = $this->newModel();

        $result = $this->getService()->removeAttribute(
            $model,
            'name'
        )->run();

        $final = <<<'EOD'
        <?php
        
        use Illuminate\Database\Eloquent\Model;
        
        return new class extends Model
        {
            protected $table = 'parrots';
        
            protected $fillable = [];
        
            protected $casts = [];
        };
        
        EOD;

        $this->assertEquals($final, $result->get(ModelBuilder::class)->first());

        $final = <<<'EOD'
        Schema::table('parrots', function (Blueprint $table) {
            $table->dropColumn('name');
        });
        EOD;
        $this->assertEquals($final, $result->get(MigrationBuilder::class)->first()->get('up'));

        // Default must be present
        $final = <<<'EOD'
        Schema::table('parrots', function (Blueprint $table) {
            $table->string('name')->default('Apollo');
        });
        EOD;
        $this->assertEquals($final, $result->get(MigrationBuilder::class)->first()->get('down'));

        $this->artisan('migrate');

        $this->assertEquals(null, $this->newModel()->create([])->name);
    }
}
