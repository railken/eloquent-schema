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
use Railken\Template\Generators;

class ModelBlueprint
{
    public string $table;
    public Collection $attributes;
    public ActionCase $action;

    public function __construct(ActionCase $action)
    {
        $this->action($action);
    }

    public function action(ActionCase $action)
    {
        $this->action = $action;
    }

    public function table(string $table)
    {
        $this->table = $table;

        return $this;
    }

    public function addAttribute(AttributeBlueprint $attribute)
    {
        $this->attributes[$attribute->name] = $attribute;
    }

}
