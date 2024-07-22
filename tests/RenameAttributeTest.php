<?php

namespace Tests;

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Railken\EloquentSchema\Builders\MigrationBuilder;
use Railken\EloquentSchema\Builders\ModelBuilder;

#[RunTestsInSeparateProcesses]
class RenameAttributeTest extends BaseCase
{
    public function test_rename()
    {
        $model = $this->newModel();

        $result = $this->getService()->renameAttribute(
            $model,
            'name',
            'title'
        )->run();

        $final = <<<'EOD'
        <?php
        
        use Illuminate\Database\Eloquent\Casts\Attribute;
        use Illuminate\Database\Eloquent\Model;
        return new class extends Model
        {
            protected $table = 'parrots';

            protected $fillable = [
                'title',
            ];

            protected $casts = [
                'title' => 'string',
            ];
            protected function name() : Attribute
            {
                return Attribute::make(get: fn(?string $value) => $this->title, set: fn(?string $value) => $this->title = $value);
            }
        };
        EOD;

        $this->assertEquals($final, $result->get(ModelBuilder::class)->first());

        $final = <<<'EOD'
        Schema::table('parrots', function (Blueprint $table) {
            $table->renameColumn('name', 'title');
        });
        EOD;
        $this->assertEquals($final, $result->get(MigrationBuilder::class)->first()->get('up'));

        $final = <<<'EOD'
        Schema::table('parrots', function (Blueprint $table) {
            $table->renameColumn('title', 'name');
        });
        EOD;
        $this->assertEquals($final, $result->get(MigrationBuilder::class)->first()->get('down'));

        $this->artisan('migrate');

        $this->newModel()->create([
            'title' => 'Cookie',
        ]);

        $this->assertEquals('Cookie', $this->newModel()->where('id', 1)->first()->title);
        $this->assertEquals('Cookie', $this->newModel()->where('id', 1)->first()->name);
    }
}
