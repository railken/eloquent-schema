<?php

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Railken\EloquentSchema\Actions\Action;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Blueprints\Attributes\StringAttribute;
use Tests\Generated\Bar;
use Tests\Generated\Baz;
use Tests\Generated\Foo;

#[RunTestsInSeparateProcesses]
class DataSchemaExportTest extends BaseCase
{
    public function getService()
    {
        return app('eloquent.schema');
    }

    /**
     * Call this template method before each test method is run.
     */
    public function setUp(): void
    {
        parent::setUp();

        if (!str_contains($this->name(), "Generated")) {
            File::deleteDirectory(__DIR__."/Generated");
            File::copyDirectory(__DIR__.'/Source', __DIR__."/Generated");
            File::cleanDirectory(__DIR__ . '/../vendor/orchestra/testbench-core/laravel/database/migrations');

            $this->artisan("migrate:fresh");
        }

        $this->getService()->addModelFolders([
            __DIR__."/Generated"
        ]);

        $this->getService()->addMigrationFolders([
            __DIR__."/Generated/migrations"
        ]);

        $this->artisan("migrate");

    }

    public function assertActionModel(Action $action, string $filename = null)
    {
        $action->run();

        if ($filename == null) {
            // @todo: handle multiple results resources
            $filename = __DIR__ . "/resources/" . $this->name() . ".txt";
        }


        foreach ($action->getResult() as $filepath => $content) {

            if (!file_exists($filename)) {
                throw new \Exception(sprintf("Missing file for test %s. Generated: \n%s", $filename, $content));
            }

            $this->assertEquals(
                trim(str_replace(["\r", "\n", "\t", " "], "", file_get_contents($filename))),
                trim(str_replace(["\r", "\n", "\t", " "], "", $content))
            );
        }
    }
    public function assertActionMigration($action)
    {
        $this->assertActionModel($action, __DIR__."/resources/".$this->name()."_migration.txt");
    }

    public function testBasic()
    {
        $this->assertTrue(file_exists(__DIR__."/Generated"));

        $this->assertEquals($this->getService()->getModels()->get("foos"), Foo::class);
        $this->assertEquals($this->getService()->getModels()->get("bars"), Bar::class);
    }

    public function testIsSimpleModel()
    {
        $class = $this->getService()->getModels()->get("foos");
        $this->assertInstanceOf(Model::class, new $class());

    }

    public function testAddAttributeStringFillable()
    {
        $model = new Foo();

        $action = $this->getService()->getModelBuilder()->createAttribute(
            $model->getTable(),
            StringAttribute::make("fillable_field")->fillable(true)
        );

        $this->assertActionModel($action);
    }

    #[Depends("testAddAttributeStringFillable")]
    public function testAddAttributeStringFillableGenerated()
    {
        $model = new Foo();
        $model->fill([
            'fillable_field' => "Fillable field"
        ]);

        $this->assertEquals([
            "fillable_field" => "Fillable field"
        ], $model->toArray());
    }

    public function testAddAttributeStringNotFillable()
    {
        $model = new Foo();

        $action = $this->getService()->getModelBuilder()->createAttribute(
            $model->getTable(),
            StringAttribute::make("guarded_field")->fillable(false)
        );

        $this->assertActionModel($action);
    }

    #[Depends("testAddAttributeStringNotFillable")]
    public function testAddAttributeStringNotFillableGenerated()
    {
        $model = new Foo();
        $model->fill([
            'random_field' => "Random field",
            'guarded_field' => "A new guarded field"
        ]);

        $this->assertEquals([

        ], $model->toArray());
    }

    public function testAddAttributeStringFillableAndNot()
    {
        $model = new Foo();

        $action = $this->getService()->getModelBuilder()->createAttribute(
            $model->getTable(),
            StringAttribute::make("fillable_field")->fillable(true)
        );

        $action->run(); // Skip check, just run

        $action = $this->getService()->getModelBuilder()->createAttribute(
            $model->getTable(),
            StringAttribute::make("guarded_field")->fillable(false)
        );

        $this->assertActionModel($action);
    }

    #[Depends("testAddAttributeStringFillableAndNot")]
    public function testAddAttributeStringFillableAndNotGenerated()
    {
        $model = new Foo();
        $model->fill([
            'fillable_field' => "Fillable field",
            'guarded_field' => "A new guarded field"
        ]);

        $this->assertEquals([
            "fillable_field" => "Fillable field"
        ], $model->toArray());
    }


    public function testAddAttributeStringFillableWithMigration()
    {
        $model = new Foo();

        $action = $this->getService()->getModelBuilder()->createAttribute(
            $model->getTable(),
            StringAttribute::make("fillable_field")->fillable(true)
        );

        $this->assertActionModel($action);

        $action = $this->getService()->getMigrationBuilder()->createAttribute(
            $model->getTable(),
            StringAttribute::make("fillable_field")->fillable(true)
        );

        $this->assertActionMigration($action);
    }

    #[Depends("testAddAttributeStringFillableWithMigration")]
    public function testAddAttributeStringFillableWithMigrationGenerated()
    {
        Foo::create([
            "fillable_field" => "Hello"
        ]);

        $this->assertEquals("Hello", Foo::where('id', 1)->first()->fillable_field);
    }

    public function testRemoveAttributeStringFillableWithMigration()
    {
        $model = new Bar();

        Bar::create([
            "name" => "Hello"
        ]);

        $this->assertEquals("Hello", Bar::where('id', 1)->first()->name);

        $action = $this->getService()->getModelBuilder()->removeAttribute(
            $model->getTable(),
            "name"
        );

        $this->assertActionModel($action);

        $action = $this->getService()->getMigrationBuilder()->removeAttribute(
            $model->getTable(),
            "name"
        );

        $this->assertActionMigration($action);
    }

    #[Depends("testRemoveAttributeStringFillableWithMigration")]
    public function testRemoveAttributeStringFillableWithMigrationGenerated()
    {
        Bar::create([]);

        $this->assertEquals(null, Bar::where('id', 1)->first()->name);
    }

    public function testRemoveAttributeStringFillableMultipleWithMigration()
    {
        $model = new Baz();

        Baz::create([
            "name" => "Hello",
            "description" => "There"
        ]);

        $this->assertEquals("Hello", Baz::where('id', 1)->first()->name);
        $this->assertEquals("There", Baz::where('id', 1)->first()->description);

        $action = $this->getService()->getModelBuilder()->removeAttribute(
            $model->getTable(),
            "description"
        );

        $this->assertActionModel($action);

        $action = $this->getService()->getMigrationBuilder()->removeAttribute(
            $model->getTable(),
            "description"
        );

        $this->assertActionMigration($action);
    }

    #[Depends("testRemoveAttributeStringFillableMultipleWithMigration")]
    public function testRemoveAttributeStringFillableMultipleWithMigrationGenerated()
    {
        Baz::create([
            "name" => "Hello2"
        ]);

        $this->assertEquals("Hello2", Baz::where('id', 2)->first()->name);
        $this->assertEquals(null, Baz::where('id', 2)->first()->description);
    }

    public function testRemoveAttributeIdWithMigration()
    {
        $model = new Bar();

        Bar::create([
            "name" => "Hello"
        ]);

        $this->assertEquals("Hello", Bar::where('id', 1)->first()->name);

        $action = $this->getService()->getModelBuilder()->removeAttribute(
            $model->getTable(),
            "id"
        );

        $this->assertActionModel($action);

        $action = $this->getService()->getMigrationBuilder()->removeAttribute(
            $model->getTable(),
            "id"
        );

        $this->assertActionMigration($action);
    }

    #[Depends("testRemoveAttributeIdWithMigration")]
    public function testRemoveAttributeIdWithMigrationGenerated()
    {
        Bar::create([
            "name" => "Hello"
        ]);

        $this->assertEquals(null, Bar::where("name", "Hello")->first()->id);
    }

    public function testRenameAttribute()
    {
        $model = new Baz();

        Baz::create([
            "name" => "Hello",
            "description" => "There"
        ]);

        $this->assertEquals("There", Baz::where('id', 1)->first()->description);

        $action = $this->getService()->getModelBuilder()->renameAttribute(
            $model->getTable(),
            "description",
            "summary"
        );

        $this->assertActionModel($action);

        $action = $this->getService()->getMigrationBuilder()->renameAttribute(
            $model->getTable(),
            "description",
            "summary"
        );

        $this->assertActionMigration($action);
    }

    #[Depends("testRenameAttribute")]
    public function testRenameAttributeGenerated()
    {
        $this->assertEquals("There", Baz::where('id', 1)->first()->summary);
        $this->assertEquals("There", Baz::where('id', 1)->first()->description); // Legacy preserved

        Baz::create([
            "name" => "Hello2",
            // "description" => "Nice1",
            "summary" => "Nice1"
        ]);

        $this->assertEquals("Nice1", Baz::where('id', 2)->first()->summary);
        $this->assertEquals("Nice1", Baz::where('id', 2)->first()->description); // Legacy preserved
    }

    public function testCompactAddAttribute()
    {
        $model = new Foo();

        $result = $this->getService()->createAttribute(
            $model->getTable(),
            StringAttribute::make("fillable_field")->fillable(true)
        );

        $this->assertActionModel($result['model']);
        $this->assertActionMigration($result['migration']);
    }

    /*
    public function testAddAttributeComputed()
    {
        $this->getService()->addAttributeToCode(
            "readable"
        )->setComputed(true);
    }

    public function testRemoveAttribute()
    {

    }

    public function testUpdateAttribute()
    {

    }

    public function testRenameAttribute()
    {

    }*/
}
