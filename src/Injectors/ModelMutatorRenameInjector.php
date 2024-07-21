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

    protected function oldValuePlaceholder(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $this->newValuePlaceholder,
            set: fn (?string $value) => $this->newValuePlaceholder = $value
        );
    }

    public function leaveNode(Node $node): void
    {
        $attrs = [];

        if ($node instanceof Node\Stmt\ClassMethod && $node->name->name == "oldValuePlaceholder") {
            $this->stmts = [$node];
            $node->name->name = $this->methodName;
        }

        if ($node instanceof Node\Identifier && $node->name == "newValuePlaceholder") {
            $node->name = $this->newName;
        }

        $node->setAttributes($attrs);
    }
}
