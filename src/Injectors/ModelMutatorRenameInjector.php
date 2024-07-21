<?php

namespace Railken\EloquentSchema\Injectors;

use Illuminate\Database\Eloquent\Casts\Attribute;
use PhpParser\BuilderFactory;
use PhpParser\Node;

class ModelMutatorRenameInjector extends MethodInjector
{
    protected string $newName;

    public function __construct(string $methodName, string $newName)
    {
        parent::__construct($methodName);

        $this->newName = $newName;
    }

    protected function methodToInject(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $this->placeholder,
            set: fn (?string $value) => $this->placeholder = $value
        );
    }

    public function leaveNode(Node $node): void {
        parent::leaveNode($node);
        if ($node instanceof Node\Identifier && $node->name == "placeholder") {
            $node->name = $this->newName;

            // return NodeVisitor::DONT_TRAVERSE_CHILDREN;
        }
    }
}