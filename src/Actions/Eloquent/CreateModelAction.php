<?php

namespace Railken\EloquentSchema\Actions\Eloquent;

use Railken\EloquentSchema\Blueprints\ModelBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;
use Railken\EloquentSchema\Support;

class CreateModelAction extends ModelAction
{
    protected ClassEditor $classEditor;

    protected ModelBlueprint $model;

    public function __construct(ModelBlueprint $model)
    {
        $this->model = $model;

        if (isset($model->instance)) {
            $this->classEditor = new ClassEditor(Support::getPathByObject($model->instance));
        } else {
            $this->classEditor = ClassEditor::newClass($model->class, $model->workingDir);
        }
    }

    /**
     * @docs: https://github.com/nikic/PHP-Parser/blob/master/doc/component/AST_builders.markdown
     */
    public function run(): void
    {
        $factory = $this->classEditor->getBuilder();
        $nodes = [];

        if (! empty($this->model->namespace)) {
            $nodes[] = $factory->namespace($this->model->namespace)->getNode();
        }

        $nodes[] = $factory->use(\Illuminate\Database\Eloquent\Model::class)->getNode();

        $class = $factory
            ->class($this->model->class)
            ->extend('Model')
            ->setDocComment('')
            ->getNode();

        if ($this->model->anonymous) {
            $nodes[] = new \PhpParser\Node\Stmt\Return_(
                new \PhpParser\Node\Expr\New_(
                    $class
                )
            );
        } else {
            $nodes[] = $class;
        }

        $this->result = $this->classEditor->saveFromNodes($nodes);
        $path = array_keys($this->result)[0];
        $this->classEditor = new ClassEditor($path);

        $this->saveAttributes();
        $this->set($this->model);
        $this->save();
    }

    public function saveAttributes(): void
    {
        foreach ($this->model->attributes as $attribute) {
            (new CreateAttributeAction($this->classEditor, $attribute))->run();
        }
    }
}
