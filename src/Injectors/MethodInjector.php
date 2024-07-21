<?php

namespace Railken\EloquentSchema\Injectors;

use PhpParser\Node;

class MethodInjector extends Injector
{
    protected string $methodName;

    protected Node $node;

    protected string $repositoryClassName;

    protected string $repositoryMethodName;

    public function __construct(string $methodName, string $repositoryClassName, string $repositoryMethodName)
    {
        $this->methodName = $methodName;
        $this->repositoryClassName = $repositoryClassName;
        $this->repositoryMethodName = $repositoryMethodName;
    }

    public function getNode(): Node
    {
        return $this->node;
    }

    public function getRepositoryClassName(): string
    {
        return $this->repositoryClassName;
    }

    public function getRepositoryMethodName(): string
    {
        return $this->repositoryMethodName;
    }

    public function leaveNode(Node $node): void
    {
        $attrs = [];

        if ($node instanceof Node\Stmt\ClassMethod && $node->name->name == $this->repositoryMethodName) {
            $this->node = $node;
            $node->name->name = $this->methodName;
        }

        $node->setAttributes($attrs);
    }
}
