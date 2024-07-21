<?php

namespace Railken\EloquentSchema\Builders;

use Exception;
use Railken\EloquentSchema\Actions\Migration\CreateColumnAction;
use Railken\EloquentSchema\Actions\Migration\RemoveColumnAction;
use Railken\EloquentSchema\Actions\Migration\RenameColumnAction;
use Railken\EloquentSchema\Actions\Migration\UpdateColumnAction;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;

class MigrationBuilder extends Builder
{
    protected AttributeBlueprint $attribute;

    public function createAttribute(string $table, AttributeBlueprint $attribute): CreateColumnAction
    {
        $this->initializeByTable($table);

        return new CreateColumnAction($table, $this->classEditor, $attribute);
    }

    /**
     * @throws Exception
     */
    public function removeAttribute(string $table, string $attributeName): RemoveColumnAction
    {
        $this->initializeByTable($table);

        $attribute = $this->schemaRetriever->getAttributeBlueprint($table, $attributeName);

        return new RemoveColumnAction($table, $this->classEditor, $attribute);
    }

    /**
     * @throws Exception
     */
    public function renameAttribute(string $table, string $oldAttributeName, string $newAttributeName): RenameColumnAction
    {
        $this->initializeByTable($table);

        $oldAttribute = $this->schemaRetriever->getAttributeBlueprint($table, $oldAttributeName);

        $newAttribute = clone $oldAttribute;
        $newAttribute->name($newAttributeName);

        return new RenameColumnAction($this->table, $this->classEditor, $oldAttribute, $newAttribute);
    }

    /**
     * @throws Exception
     */
    public function updateAttribute(string $table, string $oldAttributeName, AttributeBlueprint $newAttribute): UpdateColumnAction
    {
        $this->initializeByTable($table);

        $oldAttribute = $this->schemaRetriever->getAttributeBlueprint($table, $oldAttributeName);

        return new UpdateColumnAction($this->table, $this->classEditor, $oldAttribute, $newAttribute);
    }
}
