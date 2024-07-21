<?php

namespace Railken\EloquentSchema\Builders;

use Exception;
use Railken\EloquentSchema\Actions\Eloquent\CreateAttributeAction;
use Railken\EloquentSchema\Actions\Eloquent\RemoveAttributeAction;
use Railken\EloquentSchema\Actions\Eloquent\RenameAttributeAction;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;
use Railken\EloquentSchema\Support;
use ReflectionException;

class ModelBuilder extends Builder
{
    /**
     * @throws ReflectionException
     */
    protected function initializeByTable(string $table): void
    {
        parent::initializeByTable($table);

        $this->classEditor = new ClassEditor(Support::getPathByObject($this->model));
    }

    /**
     * Add a new attribute to the table and the relative table
     *
     * @param string $table
     * @param AttributeBlueprint $attribute
     * @return CreateAttributeAction
     * @throws Exception
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
     * @param string $attributeName
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
