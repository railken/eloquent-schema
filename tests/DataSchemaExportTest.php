<?php

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tests\Generated\Bar;
use Tests\Generated\Foo;

#[RunTestsInSeparateProcesses]
class DataSchemaExportTest extends BaseCase
{
    public function testBasic()
    {
        $this->assertTrue(file_exists(__DIR__.'/Generated'));

        $this->assertEquals($this->getService()->getModels()->get('foos'), Foo::class);
        $this->assertEquals($this->getService()->getModels()->get('bars'), Bar::class);
    }

    public function testIsSimpleModel()
    {
        $class = $this->getService()->getModels()->get('foos');
        $this->assertInstanceOf(Model::class, new $class());

    }
}
