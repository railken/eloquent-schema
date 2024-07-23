<?php

namespace Railken\EloquentSchema\Actions\Eloquent;

use Railken\EloquentSchema\Blueprints\ModelBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;

class CreateModelAction extends ModelAction
{
    protected ModelBlueprint $model;

    public function __construct(ClassEditor $classEditor, ModelBlueprint $model)
    {
        $this->model = $model;

        parent::__construct($classEditor);
    }

    /**
     * @docs: https://github.com/nikic/PHP-Parser/blob/master/doc/component/AST_builders.markdown
     */
    public function run(): void
    {
        $factory = $this->classEditor->getBuilder();

        $nodes[] = $factory->use(\Illuminate\Database\Eloquent\Model::class)->getNode();

        $nodes[] = $factory->class($this->model->class)
            ->extend('Model')
            ->addStmt($factory
                ->property('table')
                ->makeProtected()
                ->setDefault($this->model->table)
            )->setDocComment('')
            ->getNode();

        $this->result = $this->classEditor->saveFromNodes($nodes);
    }
}
