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


    public function getStmts(): array
    {
        return $this->stmts;
    }
}
