<?php

namespace Railken\EloquentSchema\Actions\Eloquent;

use Railken\EloquentSchema\Blueprints\ModelBlueprint;

class CreateModelAction extends ModelAction
{
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

        $class = $factory->class($this->model->class)
            ->extend('Model')
            ->addStmt($factory
                ->property('table')
                ->makeProtected()
                ->setDefault($this->model->table)
            )->setDocComment('');

        $this->addIncrementing($class, $this->model);
        $this->addPrimaryKey($class, $this->model);

        $class = $class->getNode();

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
    }

    protected function addIncrementing($class, ModelBlueprint $model): void
    {
        $factory = $this->classEditor->getBuilder();

        if (! $model->incrementing) {
            $class->addStmt($factory
                ->property('incrementing')
                ->makeProtected()
                ->setDefault($this->model->incrementing)
                ->setDocComment('')
            );
        }
    }

    protected function addPrimaryKey($class, ModelBlueprint $model): void
    {
        $factory = $this->classEditor->getBuilder();

        if (! $model->primaryKey[0] !== 'id') {
            $class->addStmt($factory
                ->property('primaryKey')
                ->makeProtected()
                ->setDefault($this->model->primaryKey)
                ->setDocComment('')
            );
        }
    }
}
