<h1 align="left">Eloquent Schema</h1>

[![Actions Status](https://github.com/railken/eloquent-schema/workflows/Test/badge.svg)](https://github.com/railken/eloquent-schema/actions)

A laravel package.

WIP.


// Simple installation
// Custom hooks

Retrieve service
```php
$schema = app('eloquent.schema');
```

Starting point

```php
namespace App\Models;

class Book
{
    protected $fillable = [
        'title'
    ];
}

```

Executing this code
```php
$model = new \App\Models\Book();

$schema->createAttribute(
    $model->getTable(),
    TextAttribute::make('description')->fillable(true)
);
```

will generate new files:


Updated Book
```php
namespace App\Models;

class Book
{
    protected $fillable = [
        'title', 
        'description'
    ];
}

```

and a new migration
```php
return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::update('books', function (Blueprint $table) {
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::update('books', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }

```


Add Models folders to scan in (Provider/Composer)
```php
$schema->addModelFolders([
    __DIR__.'/Generated',
]);

$schema->addMigrationFolders([
    __DIR__.'/Generated/migrations',
]);
```


