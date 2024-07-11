<?php

namespace Tests;

use Illuminate\Support\Facades\File;
use Tests\Generated\Laravel\Foo;
use Tests\Generated\Railken\EloquentSchema\Book;
use Illuminate\Database\Eloquent\Model;
use Railken\Lem\Contracts\EntityContract;

class SchemaRetrieverTest extends BaseCase
{

    public function testGetMigrationGeneratorSchemaByName()
    {
        $attributes = $this->getService()->getSchemaRetriever()->getMigrationGeneratorSchemaByName("bars");
        $this->assertTrue(true);
    }
}
