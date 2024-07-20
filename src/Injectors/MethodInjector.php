<?php

namespace Railken\EloquentSchema\Injectors;

use PhpParser\BuilderFactory;
use PhpParser\Node;

class MethodInjector extends Injector
{
    protected string $methodName;
    protected array $stmts;
    public function __construct(string $methodName)
    {
        $this->methodName = $methodName;
    }

    public function leaveNode(Node $node): void {

        if ($node instanceof Node\Stmt\ClassMethod && $node->name->name == "methodToInject") {

            $this->stmts = [$node];
            $node->name->name = $this->methodName;

            // return NodeVisitor::DONT_TRAVERSE_CHILDREN;
        }
    }

    public function getStmts(): array
    {
        return $this->stmts;
    }
}