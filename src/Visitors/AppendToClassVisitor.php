<?php

namespace Railken\EloquentSchema\Visitors;

use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;

class AppendToClassVisitor extends NodeVisitorAbstract
{
    protected Node $node;

    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    public function leaveNode(Node $node): void
    {
        if ($node instanceof Node\Stmt\Class_) {
            $node->stmts[] = $this->node;
        }
    }

}
