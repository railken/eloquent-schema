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
use PhpParser\Node\Expr\StaticCall;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Expr\Closure;

class SchemaUpdateVisitor extends NodeVisitorAbstract
{
    protected $result;
    protected $validNameTables = [
        StaticCall::class,
        String_::class,
    ];

    public function enterNode(Node $node)
    {
        if ($node instanceof StaticCall) {
            $parts = $node->class->toCodeString();

            if ($parts == "\\".Schema::class && in_array($node->name->name, ['create', 'table'])) {
                $this->result = $node;


                if (count($node->args) == 2) {

                    if ($this->validInstance($node->args[0]->value)) {

                        $dumper = new NodeDumper();

                        $prettyPrinter = new PrettyPrinter\Standard();
                        $code = str_replace("<?php\n\n", "return ", $prettyPrinter->prettyPrintFile([$node->args[0]->value]));

                        try {
                            $tableName = eval($code.";");
                        } catch (\Exception $e) {
                            throw new \Exception(sprintf("An error was found in the migration: %s", $e));
                        }

                    }

                    if (!isset($tableName)) {
                        throw new \Exception(sprintf("Couldn't find a tableName in the migration: %s", $parts));
                    }

                    if (!($node->args[1]->value instanceof Closure)) {
                        throw new \Exception(sprintf("Second parameter should be a Closure in your migration"));
                    }
                }
            }
        }
    }

    public function validInstance(Node $node)
    {
        foreach ($this->validNameTables as $validNameTable) {
            if ($node instanceof $validNameTable) {
                return true;
            }
        }

        return false;
    }

    public function getResult()
    {
        return $this->result;
    }
}
