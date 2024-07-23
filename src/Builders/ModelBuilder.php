<?php

namespace Railken\EloquentSchema\Builders;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Railken\EloquentSchema\Actions\Eloquent\Attribute;
use Railken\EloquentSchema\Actions\Eloquent\CreateAttributeAction;
use Railken\EloquentSchema\Actions\Eloquent\CreateModelAction;
use Railken\EloquentSchema\Actions\Eloquent\RemoveAttributeAction;
use Railken\EloquentSchema\Actions\Eloquent\RemoveModelAction;
use Railken\EloquentSchema\Actions\Eloquent\RenameAttributeAction;
use Railken\EloquentSchema\Actions\Eloquent\UpdateAttributeAction;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;
use Railken\EloquentSchema\Blueprints\ModelBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;
use Railken\EloquentSchema\Support;
use ReflectionException;

class ModelBuilder extends Builder
{
    /**
     * @throws ReflectionException
     */
    protected function initialize(string|Model $ini): ModelBuilder
    {
        parent::initialize($ini);

        $this->classEditor = new ClassEditor(Support::getPathByObject($this->model));

        return $this;
    }

    public function createModel(ModelBlueprint $model): CreateModelAction
    {

        if (empty($model->workingDir)) {
            $model->workingDir = $this->schemaRetriever->getFolders()->first();
        }

        $model->updateNameSpaceToWorkingDir();

        $this->classEditor = ClassEditor::newClass($model->class, $model->workingDir);

        return new CreateModelAction($this->classEditor, $model);
    }

    public function removeModel(string $ini): RemoveModelAction
    {
        $this->initialize($ini);
        $model = new ModelBlueprint($ini);

        return new RemoveModelAction($this->classEditor, $model);
    }

    /**
     * Add a new attribute to the table and the relative model
     *
     * @throws Exception
     */
    public function createAttribute(string|Model $ini, AttributeBlueprint $attribute): CreateAttributeAction
    {
        $this->initialize($ini);

        return new CreateAttributeAction($this->classEditor, $attribute);
    }

    /**
     * Remove an attribute from the table and the relative model
     *
     * @throws Exception
     */
    public function removeAttribute(string|Model $ini, string $attributeName): RemoveAttributeAction
    {
        $this->initialize($ini);

        $attribute = $this->schemaRetriever->getAttributeBlueprint($this->table, $attributeName);

        return new RemoveAttributeAction($this->classEditor, $attribute);
    }

    /**
     * Rename an attribute
     *
     * @throws Exception
     */
    public function renameAttribute(string|Model $ini, string $oldAttributeName, string $newAttributeName): RenameAttributeAction
    {
        $this->initialize($ini);

        $oldAttribute = $this->schemaRetriever->getAttributeBlueprint($this->table, $oldAttributeName);

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
    public function updateAttribute(string|Model $ini, string $attributeName, AttributeBlueprint $newAttribute): UpdateAttributeAction
    {
        $this->initialize($ini);

        $oldAttribute = $this->schemaRetriever->getAttributeBlueprint($this->table, $attributeName);

        Attribute::callHooks('set', [$this->classEditor, $oldAttribute]);

        return new UpdateAttributeAction($this->classEditor, $oldAttribute, $newAttribute);
    }
}
