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
        $this->save();
    }

    public function saveAttributes(): void
    {
        foreach ($this->newModel->diffAttributes($this->oldModel) as $attribute) {
            (new CreateAttributeAction($this->classEditor, $attribute))->run();
        }

        foreach ($this->oldModel->diffAttributes($this->newModel) as $attribute) {
            (new RemoveAttributeAction($this->classEditor, $attribute))->run();
        }

        foreach ($this->oldModel->sameAttributes($this->newModel) as $diff) {
            (new UpdateAttributeAction($this->classEditor, $diff->oldAttribute, $diff->newAttribute))->run();
        }
    }
}
