<?php

namespace Railken\EloquentSchema\Actions\Eloquent;

use Railken\EloquentSchema\Blueprints\ModelBlueprint;
use Railken\EloquentSchema\Editors\ClassEditor;
use Railken\EloquentSchema\Support;

class CreateModelAction extends ModelAction
{
    protected ClassEditor $classEditor;

    protected ModelBlueprint $newModel;

    public function __construct(ModelBlueprint $newModel)
    {
        $this->newModel = $newModel;

        if (isset($newModel->instance)) {
            $this->classEditor = new ClassEditor(Support::getPathByObject($newModel->instance));
        } else {
            $this->classEditor = ClassEditor::newClass($newModel->class, $newModel->workingDir);
        }
    }

    /**
     * @docs: https://github.com/nikic/PHP-Parser/blob/master/doc/component/AST_builders.markdown
     */
    public function run(): void
    {
        $nodes = $this->createNewClass();

        $this->result = $this->classEditor->saveFromNodes($nodes);
        $path = array_keys($this->result)[0];
        $this->classEditor = new ClassEditor($path);

        $this->saveAttributes();
        $this->mutate($this->newModel);
        $this->save();
    }

    public function createNewClass(): array
    {
        $factory = $this->classEditor->getBuilder();
        $nodes = [];

        if (! empty($this->newModel->namespace)) {
            $nodes[] = $factory->namespace($this->newModel->namespace)->getNode();
        }

        $nodes[] = $factory->use(\Illuminate\Database\Eloquent\Model::class)->getNode();

        $class = $factory
            ->class($this->newModel->class)
            ->extend('Model')
            ->setDocComment('')
            ->getNode();

        if ($this->newModel->anonymous) {
            $nodes[] = new \PhpParser\Node\Stmt\Return_(
                new \PhpParser\Node\Expr\New_(
                    $class
                )
            );
        } else {
            $nodes[] = $class;
        }

        return $nodes;
    }

    public function saveAttributes(): void
    {
        foreach ($this->newModel->attributes as $attribute) {
            (new CreateAttributeAction($this->classEditor, $attribute))->run();
        }
    }
}
