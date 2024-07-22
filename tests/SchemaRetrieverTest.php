<?php

namespace Tests;

class SchemaRetrieverTest extends BaseCase
{
    public function testGetMigrationGeneratorSchemaByName()
    {
        $attributes = $this->getService()->getSchemaRetriever()->getMigrationGeneratorSchemaByName('parrots');
        $this->assertTrue(true);
    }
}
