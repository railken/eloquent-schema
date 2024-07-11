<?php

namespace Railken\EloquentSchema;

use Illuminate\Database\Eloquent\Model;
use Archetype\Facades\PHPFile;
use ReflectionClass;
use Illuminate\Support\Collection;
use PhpParser\PrettyPrinter;
use PhpParser\NodeFinder;
use PhpParser\NodeDumper;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitor;

class SchemaUpVisitor extends NodeVisitorAbstract
{
    protected $result;

    public function leaveNode(Node $node)
    {
        if ($node instanceof ClassMethod) {

            if ($node->name->name == "up") {
                $this->result = $node;

                //return NodeVisitor::DONT_TRAVERSE_CHILDREN;
            }
        }
    }

    public function getResult()
    {
        return $this->result;
    }
}
