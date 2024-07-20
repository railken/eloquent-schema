<?php

namespace Railken\EloquentSchema\Builders;

use Exception;
use Railken\EloquentSchema\Actions\Eloquent\CreateAttributeAction;
use Railken\EloquentSchema\Actions\Eloquent\RemoveAttributeAction;
use Railken\EloquentSchema\Actions\Eloquent\RenameAttributeAction;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

class ModelBuilder extends Builder
{
    protected function initializeByTable(string $table): void
    {
        parent::initializeByTable($table);

        $reflector = new \ReflectionClass(get_class($this->model));
        $path = $reflector->getFileName();

        $this->classEditor = new ClassEditor($path);
    }

    /**
     * Add a new attribute to the table and the relative table
     *
     * @param string $table
     * @param AttributeBlueprint $attribute
     * @return CreateAttributeAction
     */
    public function createAttribute(string $table, AttributeBlueprint $attribute): CreateAttributeAction
    {
        $this->initializeByTable($table);
        return new CreateAttributeAction($this->classEditor, $attribute);
    }

    /**
     * Remove an attribute from the table and the relative model
     *
     * @param string $table
     * @param string $attribute
     * @return RemoveAttributeAction
     * @throws Exception
     */
    public function removeAttribute(string $table, string $attributeName): RemoveAttributeAction
    {
        $this->initializeByTable($table);

        $attribute = $this->schemaRetriever->getAttributeBlueprint($table, $attributeName);

        return new RemoveAttributeAction($this->classEditor, $attribute);
    }

    /**
     * Remove an attribute from the table and the relative model
     *
     * @param string $table
     * @param string $oldAttributeName
     * @param string $newAttributeName
     * @return RenameAttributeAction
     * @throws Exception
     */
    public function renameAttribute(string $table, string $oldAttributeName, string $newAttributeName): RenameAttributeAction
    {
        $this->initializeByTable($table);

        $oldAttribute = $this->schemaRetriever->getAttributeBlueprint($table, $oldAttributeName);

        $newAttribute = clone $oldAttribute;
        $newAttribute->name($newAttributeName);

        return new RenameAttributeAction($this->classEditor, $oldAttribute, $newAttribute);
    }
}
