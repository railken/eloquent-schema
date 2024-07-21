<?php

namespace Railken\EloquentSchema\Builders;

use Exception;
use Railken\EloquentSchema\Actions\Eloquent\Attribute;
use Railken\EloquentSchema\Actions\Eloquent\CreateAttributeAction;
use Railken\EloquentSchema\Actions\Eloquent\RemoveAttributeAction;
use Railken\EloquentSchema\Actions\Eloquent\RenameAttributeAction;
use Railken\EloquentSchema\Actions\Eloquent\UpdateAttributeAction;
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
     * Add a new attribute to the table and the relative model
     *
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
     * @throws Exception
     */
    public function removeAttribute(string $table, string $attributeName): RemoveAttributeAction
    {
        $this->initializeByTable($table);

        $attribute = $this->schemaRetriever->getAttributeBlueprint($table, $attributeName);

        return new RemoveAttributeAction($this->classEditor, $attribute);
    }

    /**
     * Rename an attribute
     *
     * @throws Exception
     */
    public function renameAttribute(string $table, string $oldAttributeName, string $newAttributeName): RenameAttributeAction
    {
        $this->initializeByTable($table);

        $oldAttribute = $this->schemaRetriever->getAttributeBlueprint($table, $oldAttributeName);

        Attribute::callHooks('set', [$this->classEditor, $oldAttribute]);

        $newAttribute = clone $oldAttribute;
        $newAttribute->name($newAttributeName);

        return new RenameAttributeAction($this->classEditor, $oldAttribute, $newAttribute);
    }

    /**
     * update an attribute
     *
     * @throws Exception
     */
    public function updateAttribute(string $table, string $attributeName, AttributeBlueprint $newAttribute): UpdateAttributeAction
    {
        $this->initializeByTable($table);

        $oldAttribute = $this->schemaRetriever->getAttributeBlueprint($table, $attributeName);

        Attribute::callHooks('set', [$this->classEditor, $oldAttribute]);

        return new UpdateAttributeAction($this->classEditor, $oldAttribute, $newAttribute);
    }
}
