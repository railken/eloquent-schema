<h1 align="left">Eloquent Schema</h1>

[![Actions Status](https://github.com/railken/eloquent-schema/workflows/Test/badge.svg)](https://github.com/railken/eloquent-schema/actions)
# Eloquent Schema

WIP.

This file will be heavily modified as the development goes


This package will offer the following functionality:
- Manipulation of the code: create, edit and delete classes (mostly Eloquent Models) all over your project programmatically
- Dynamic Eloquent Models: create, edit and delete models, columns, indexes, relationships without changing the code
- Sync between the code and the dynamic models: Update the code based on dynamic models and vice-versa


### Scenario example

Let's say we have a model: Parrot
```php
use Illuminate\Database\Eloquent\Model;

class Parrot extends Model
{
    protected $table = 'parrots';

    protected $fillable = [
        'name'
    ];

    protected $casts = [
        'name' => 'string'
    ];
};

```

Our user want's to add a new attribute to better describe the Parrot, so through a controller the user adds a new record
```php
use Railken\EloquentSchema\Models\Schema;

Schema::create([
    'table' => 'parrots',
    'column' => 'description',
    'payload' => Yaml::dump([
        'type' => 'text'
    ]);
])
    
```

The new field can now be used in any part of your project
```php
$params = $request->all();

$parrot = new Parrot($params);

$parrot->toArray();
// [
//     'name' => 'Chip',
//     'description' => 'A cool parrot'
// ]

```

Of course to handle attributes made by the user your controllers/serializers must handle the logic of dynamic fields [WIP with example], and all of your views must be able to show all the fields: A perfect case would be any administration panel.


This package is not only limited by attributes, you can handle many other things: relationships, new tables, renaming, deleting attributes, etc...

[WIP/Link examples].


Now, suppose the user adds tons of new data but some of them requires some more in depth logic. It's time for you to get your hands dirty and start coding.

There is an endpoint fully customizable to which you can connect [WIP/examples] to retrieve all the `Schema` that the user created. This will let you connect to a production/stage endpoint and download all the user changes. If you prefer to use other methods for the sync, just remember to update the table `schema`.




Simply run `php artisan schema:download`

If you have configured correctly your configuration, this will update your current table `Schema`, so now you have access as well of all the dynamic changes that the user made.

Problem is that your code still misses the new changes, and while it works dinamically you would prefer to work with a code that's aligned to the data you are working with it.

Simply run `php artisan schema:update:code`.

Now, the parrot that we previously saw would look like this

```php
use Illuminate\Database\Eloquent\Model;

class Parrot extends Model
{
    protected $table = 'parrots';

    protected $fillable = [
        'name',
        'description'
    ];

    protected $casts = [
        'name' => 'string',
        'description' => 'string'
    ];
};

```

After you finish updating your new code with your funky shenanigans launch this command both on your local and production environemnt `php artisan schema:resync`. This command will tell to use the `description` from the code and not load it dinamically anymore. Should even improve some performances.

### Customization

Now, of course each project is different and has different needs. For each attribute or model that is added you might need different files, different changes. This is possibile by using Resolvers and Hooks

WIP

```php
use Railken\EloquentSchema\Actions\Eloquent\Attribute;
use Railken\EloquentSchema\Actions\Migration\Column;

Attribute::setHooks([
    FillableHook::class,
    GuardedHook::class,
    CastHook::class,
]);

??
Column::setHooks([
    DefaultHook::class,
    NullableHook::class,
]);


app('eloquent.schema')->setResolvers([
    ModelBuilder::class,
    MigrationBuilder::class,
]);
```


### Generation code


```php
$helper = app('eloquent.schema');
```
#### Models

Creating a new Model
```php
$helper->createModel(ModelBlueprint::make('duck')))->run();


$helper->createModel(ModelBlueprint::make('duck')->attributes(
    IntegerAttribute::make('name')
        ->fillable(true),
    IntegerAttribute::make('type')
        ->fillable(true)
        ->default(5)
))->run();

$helper->createModel(ModelBlueprint::make('duck')->attributes(
    IntegerAttribute::make('name')
        ->fillable(true),
    IntegerAttribute::make('type')
        ->fillable(true)
        ->default(5)
)->primary('name')->incrementing(false))->run();
```

Updating a model. Sending attributes on update model will overwrite the current one. So in this case
name will be required, and type will be removed
```php
$helper->updateModel(Duck::class, ModelBlueprint::make('duck')->attributes([
    IntegerAttribute::make('name')
        ->fillable(true)
        ->required(true)
])->run();
```

#### Attributes
Adding a new attribute
```php
$helper->createAttribute(
    Duck::class,
    IntegerAttribute::make('type')
        ->fillable(true)
        ->default(5)
)->run();
```

Updating an attribute
```php
$helper->updateAttribute(
    Duck::class,
    'type',
    IntegerAttribute::make('type')
        ->fillable(true)
        ->default(5)
)->run();
```

Removing an attribute
```php
$helper->removeAttribute(
    Duck::class,
    'type'
)->run();
```

Renaming an attribute
```php
$helper->renameAttribute(
    Duck::class,
    'type',
    'newName'
)->run();
```



### Flow
- User adds new tables, columns, indexes, relations from an API/Admin panel
- New columns/tables/indexes are handled dinamically and data starts to populate. No need to update the code
- Dev needs to use some tables/columns created by the user
- Import specific tables from production. Maybe an endpoint to production with credentials to sync tables. This will update the table schema
- Dev has now the same columns/tables/indexes handled dinamically
- Dev launch a command and all the new data will generate new code, new models, new migrations, etc.... Table schema is updated and will take from source code, instead of the dynamic table
- Dev can use those fields/models as much as he wants
- New migrations are created for the local db of dev
- Dev changes some attributes to improve some efficiency
- Dev push new code
- Server production launch a command to resync data dynamic and data from source
- While resync in order to verify who wins between code or dynamic field @updated_at (attribute/comment) is used
- Migrations will not be executed as those tables/columns/indexes/releations were already created
- Production is faster now, and code is in line with the data




# Example usages

# Configuration

table structure

creami

| data | type | name | schema | payload | source | updated_at |
| -------- | -------- | -------- |-------- |-------- |-------- |-------- |
| book     | attribute     | id     | id     | primary key?  | code | date |
| book     | attribute     | author_id     | int      | foreign_key\ntse |  user | date |
| book     | relation     | author     | belongs_to     |  foreignKey: author_id, foreignData: author, externalId: id| user | date |
| book     | attribute     | code     | string     | min:1, max: 16, regex: 1029 | user | date |


### Export code data to database
`php artisan data:export`

This will generate a tree of all data, attributes and relations from the code and write them in the table.
It will overwrite (1) all current records with the same data+attribute|relation.
It will not overwrite extra records, so attributes generated from user will not be overwritten


Node: maybe check @updatedAt to see if overwrite or not
### Export database to code
`php artisan data:import`

This will generate php code from the attributes that are not present in the code.

For attributes it will generate a migration as well

Each code generated will have a comment with @updatedAt or similar.



```php
    /**
     * @updatedAt 2024-07-03T13:20Z10:10:00
     *
     * @return BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class)
    }
```

### Conflicts between code and table
@updatedAt is checked against updated_at of the table, if missing or lower the table will win, if higher the code will win. latest updated_at will win


### Updating code
`php artisan data:import`

When doing this command it will find the current method and update it in case of conflict


### Handling renaming

Perhaps using migrations in the table can help with renaming
using setAttribute, getAttribute for compatibility


### Adding "computed" attributes
Attributes that are not saved in the db, but only readable



