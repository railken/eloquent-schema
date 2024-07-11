<?php

namespace Tests;

use Illuminate\Support\Facades\File;
use Tests\Generated\Foo;
use Tests\Generated\Bar;
use Tests\Generated\Baz;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Depends;
use Railken\EloquentSchema\AttributeBlueprint;

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

    public function assertResourceModel($builder)
    {
        $filename = __DIR__."/resources/".$this->name().".txt";

        if (!file_exists($filename)) {
            throw new \Exception(sprintf("Missing file for test %s. Generated: \n%s", $filename, $builder->render()));
        }
        $this->assertEquals(
            trim(str_replace(["\r","\n","\t", " "], "", file_get_contents($filename))),
            trim(str_replace(["\r","\n","\t", " "], "", $builder->render()))
        );
    }

    public function assertResourceMigration($builder)
    {
        $filename = __DIR__."/resources/".$this->name()."_migration.txt";

        if (!file_exists($filename)) {
            throw new \Exception(sprintf("Missing file for test %s. Generated: \n%s", $filename, $builder->render()));
        }

        $this->assertEquals(
            trim(str_replace(["\r","\n","\t", " "], "", file_get_contents($filename))),
            trim(str_replace(["\r","\n","\t", " "], "", $builder->render()))
        );
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

        $builder = $this->getService()->getModelBuilder()->createAttribute(
            $model->getTable(),
            function (AttributeBlueprint $attribute) {
                $attribute->name("fillable_field")->type("string")->fillable(true);
            }
        );

        $builder->save();

        $this->assertResourceModel($builder);
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

        $builder = $this->getService()->getModelBuilder()->createAttribute(
            $model->getTable(),
            function (AttributeBlueprint $attribute) {
                $attribute->name("guarded_field")->type("string")->fillable(false);
            }
        );

        $builder->save();

        $this->assertResourceModel($builder);
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

        $builder = $this->getService()->getModelBuilder()->createAttribute(
            $model->getTable(),
            function (AttributeBlueprint $attribute) {
                $attribute->name("fillable_field")->type("string")->fillable(true);
            }
        );

        $builder->save();

        $builder = $this->getService()->getModelBuilder()->createAttribute(
            $model->getTable(),
            function (AttributeBlueprint $attribute) {
                $attribute->name("guarded_field")->type("string")->fillable(false);
            }
        );

        $builder->save();

        $this->assertResourceModel($builder);
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

        $builder = $this->getService()->getModelBuilder()->createAttribute(
            $model->getTable(),
            function (AttributeBlueprint $attribute) {
                $attribute->name("fillable_field")->type("string")->fillable(true);
            }
        );

        $builder->save();

        $this->assertResourceModel($builder);

        $builder = $this->getService()->getMigrationBuilder()->createAttribute(
            $model->getTable(),
            function (AttributeBlueprint $attribute) {
                $attribute->name("fillable_field")->type("string")->fillable(true);
            }
        );
        $builder->save();

        $this->assertResourceMigration($builder);
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

        $builder = $this->getService()->getModelBuilder()->removeAttribute(
            $model->getTable(),
            "name"
        );

        $builder->save();

        $this->assertResourceModel($builder);

        $builder = $this->getService()->getMigrationBuilder()->removeAttribute(
            $model->getTable(),
            "name"
        );
        $builder->save();

        $this->assertResourceMigration($builder);
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

        $builder = $this->getService()->getModelBuilder()->removeAttribute(
            $model->getTable(),
            "description"
        );

        $builder->save();

        $this->assertResourceModel($builder);

        $builder = $this->getService()->getMigrationBuilder()->removeAttribute(
            $model->getTable(),
            "description"
        );
        $builder->save();

        $this->assertResourceMigration($builder);
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

        $builder = $this->getService()->getModelBuilder()->removeAttribute(
            $model->getTable(),
            "id"
        );

        $builder->save();

        $this->assertResourceModel($builder);

        $builder = $this->getService()->getMigrationBuilder()->removeAttribute(
            $model->getTable(),
            "id"
        );
        $builder->save();

        $this->assertResourceMigration($builder);
    }

    #[Depends("testRemoveAttributeIdWithMigration")]
    public function testRemoveAttributeIdWithMigrationGenerated()
    {
        Bar::create([
            "name" => "Hello"
        ]);

        $this->assertEquals(null, Bar::where("name", "Hello")->first()->id);
    }

    public function testCompactAddAttribute()
    {
        $model = new Foo();

        [$modelBuilder, $migrationBuilder] = $this->getService()->createAttribute(
            $model->getTable(),
            function (AttributeBlueprint $attribute) {
                $attribute->name("fillable_field")->type("string")->fillable(true);
            }
        );

        $this->assertResourceModel($modelBuilder);
        $this->assertResourceMigration($migrationBuilder);
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
