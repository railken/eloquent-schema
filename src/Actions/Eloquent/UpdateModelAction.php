<?php

namespace Railken\EloquentSchema\Actions\Eloquent;

use Railken\EloquentSchema\Blueprints\ModelBlueprint;

class UpdateModelAction extends CreateModelAction
{
    protected ModelBlueprint $oldModel;

    protected ModelBlueprint $newModel;

    public function __construct(ModelBlueprint $oldModel, ModelBlueprint $newModel)
    {
        $this->oldModel = $oldModel;
        $this->newModel = $newModel;

        parent::__construct($newModel);
    }

    /**
     * @docs: https://github.com/nikic/PHP-Parser/blob/master/doc/component/AST_builders.markdown
     */
    public function run(): void
    {
        $this->saveAttributes();
        $this->set($this->newModel);
        $this->result = $this->classEditor->save();
    }

    public function saveAttributes(): void
    {
        $attributesToAdd = array_diff(
            array_keys($this->newModel->attributes),
            array_keys($this->oldModel->attributes)
        );

        foreach ($attributesToAdd as $attributeName) {
            (new CreateAttributeAction(
                $this->classEditor,
                $this->newModel->getAttributeByName($attributeName)
            ))->run();
        }

        $attributesToRemove = array_diff(
            array_keys($this->oldModel->attributes),
            array_keys($this->newModel->attributes)
        );

        foreach ($attributesToRemove as $attributeName) {
            (new RemoveAttributeAction(
                $this->classEditor,
                $this->oldModel->getAttributeByName($attributeName)
            ))->run();
        }

        $attributesToUpdate = array_intersect(
            array_keys($this->newModel->attributes),
            array_keys($this->oldModel->attributes)
        );

        foreach ($attributesToUpdate as $attributeName) {

            (new UpdateAttributeAction(
                $this->classEditor,
                $this->oldModel->getAttributeByName($attributeName),
                $this->newModel->getAttributeByName($attributeName)
            ))->run();
        }
    }
}
