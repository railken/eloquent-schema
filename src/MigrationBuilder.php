<?php

namespace Railken\EloquentSchema;

use Illuminate\Database\Eloquent\Model;
use Archetype\Facades\PHPFile;
use ReflectionClass;
use Illuminate\Support\Collection;
use PhpParser\PrettyPrinter;
use PhpParser\NodeFinder;
use PhpParser\NodeDumper;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use Railken\Template\Generators;
use Closure;

class MigrationBuilder extends Builder
{
    protected AttributeBlueprint $attributeUp;
    protected AttributeBlueprint $attributeDown;

    /**
     * Get the migration stub file.
     *
     * @return string
     */
    protected function getAttributeStub()
    {
        return file_get_contents(__DIR__."/../stubs/migration.attribute.stub");
    }

    /**
     * Populate the place-holders in the migration stub.
     *
     * @param  string  $stub
     * @param  string|null  $table
     * @param  string|null  $newValue
     * @param  string|null  $oldValue
     * @return string
     */
    protected function populateStub(string $stub, string $table, AttributeBlueprint $newValue, AttributeBlueprint $oldValue)
    {
        $generator = new Generators\HtmlGenerator();

        $rendered = $generator->generateAndRender($stub, [
            'table' => $table,
            'oldValue' => $oldValue,
            'newValue' => $newValue
        ]);

        return $rendered;
    }

    public function createAttribute(string $table, Closure $closure)
    {
        $this->initializeByTable($table);

        $this->attributeUp = new AttributeBlueprint(ActionCase::Create);
        $closure($this->attributeUp);

        $this->attributeDown = $this->attributeUp->createOpposite()->convert();
        $this->attributeUp = $this->attributeUp->convert();

        return $this;
    }

    public function removeAttribute(string $table, string $attributeName)
    {
        $this->initializeByTable($table);

        $this->attributeUp = new AttributeBlueprint(ActionCase::Remove);
        $this->attributeUp->name($attributeName);
        $this->attributeUp->fillFromDatabaseSchema($this->schemaRetriever->getMigrationGeneratorSchemaByName($table));

        $this->attributeDown = $this->attributeUp->createOpposite();

        return $this;
    }

    public function save()
    {
        file_put_contents($this->getPath(), $this->render());

        return $this;
    }

    public function render()
    {
        return $this->populateStub($this->getAttributeStub(), $this->model->getTable(), $this->attributeUp->convert(), $this->attributeDown->convert());
    }

    /**
     * Get the full path to the migration.
     *
     * @return string
     */
    protected function getPath()
    {
        $name = $this->operation."_".$this->table."_table";

        return database_path().DIRECTORY_SEPARATOR."migrations".DIRECTORY_SEPARATOR.$this->getDatePrefix().'_'.$name.'.php';
    }

    /**
     * Get the date prefix for the migration.
     *
     * @return string
     */
    protected function getDatePrefix()
    {
        return date('Y_m_d_His');
    }
}
