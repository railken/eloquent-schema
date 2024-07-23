<?php

namespace Railken\EloquentSchema\Builders;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Railken\EloquentSchema\Actions\Migration\CreateColumnAction;
use Railken\EloquentSchema\Actions\Migration\CreateTableAction;
use Railken\EloquentSchema\Actions\Migration\RemoveColumnAction;
use Railken\EloquentSchema\Actions\Migration\RemoveTableAction;
use Railken\EloquentSchema\Actions\Migration\RenameColumnAction;
use Railken\EloquentSchema\Actions\Migration\UpdateColumnAction;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Blueprints\ModelBlueprint;

class MigrationBuilder extends Builder
{
    protected AttributeBlueprint $attribute;

    public function createModel(ModelBlueprint $model): CreateTableAction
    {
        return new CreateTableAction($model);
    }

    public function removeModel(string $ini): RemoveTableAction
    {
        $this->initialize($ini);
        $model = new ModelBlueprint($ini);

        return new RemoveTableAction($this->table, $this->classEditor, $model);
    }

    public function createAttribute(string|Model $ini, AttributeBlueprint $attribute): CreateColumnAction
    {
        $this->initialize($ini);

        return new CreateColumnAction($this->table, $this->classEditor, $attribute);
    }

    /**
     * @throws Exception
     */
    public function removeAttribute(string|Model $ini, string $attributeName): RemoveColumnAction
    {
        $this->initialize($ini);

        $attribute = $this->schemaRetriever->getAttributeBlueprint($this->table, $attributeName);

        return new RemoveColumnAction($this->table, $this->classEditor, $attribute);
    }

    /**
     * @throws Exception
     */
    public function renameAttribute(string|Model $ini, string $oldAttributeName, string $newAttributeName): RenameColumnAction
    {
        $this->initialize($ini);

        $oldAttribute = $this->schemaRetriever->getAttributeBlueprint($this->table, $oldAttributeName);

        $newAttribute = clone $oldAttribute;
        $newAttribute->name($newAttributeName);

        return new RenameColumnAction($this->table, $this->classEditor, $oldAttribute, $newAttribute);
    }

    /**
     * @throws Exception
     */
    public function updateAttribute(string|Model $ini, string $oldAttributeName, AttributeBlueprint $newAttribute): UpdateColumnAction
    {
        $this->initialize($ini);

        $oldAttribute = $this->schemaRetriever->getAttributeBlueprint($this->table, $oldAttributeName);

        return new UpdateColumnAction($this->table, $this->classEditor, $oldAttribute, $newAttribute);
    }
}
