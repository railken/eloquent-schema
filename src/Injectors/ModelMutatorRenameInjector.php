<?php

namespace Railken\EloquentSchema\Injectors;

use PhpParser\Node;
use Railken\EloquentSchema\Injectors\Repositories\ModelRepository;

class ModelMutatorRenameInjector extends MethodInjector
{
    protected string $newName;

    public function __construct(string $methodName, string $newName)
    {
        parent::__construct($methodName, ModelRepository::class, 'renameMutator');

        $this->newName = $newName;
    }

    public function leaveNode(Node $node): void
    {
        if ($node instanceof Node\Identifier && $node->name == 'newValuePlaceholder') {
            $node->name = $this->newName;
        }

        parent::leaveNode($node);
    }
}
