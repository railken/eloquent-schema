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
use KitLoong\MigrationsGenerator\Enum\Migrations\Method\IndexType;

class AttributeBlueprint
{
    public string $name;
    public string $type;
    public ?bool $fillable = null;
    public ?bool $required = null;
    public ActionCase $action;
    public $types;

    public function __construct(ActionCase $action)
    {
        $this->action($action);
        $this->types = [
            'id' => Attributes\IdAttribute::class,
            'string' => Attributes\StringAttribute::class,
            'text' => Attributes\TextAttribute::class
        ];
    }

    public function action(ActionCase $action)
    {
        $this->action = $action;
    }

    public function name(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function type(string $type)
    {
        if (!in_array($type, array_keys($this->types))) {
            throw new \Exception(sprintf("Invalid type: %s", $type));
        }

        $this->type = $type;

        return $this;
    }

    public function convert()
    {
        $sub = new $this->types[$this->type]($this->action);
        $sub->name($this->name);
        $sub->type($this->type);
        $sub->fillable($this->fillable);
        $sub->required($this->required);

        return $sub;
    }

    public function fillable(?bool $fillable = true)
    {
        $this->fillable = $fillable;

        return $this;
    }

    public function required(?bool $required = true)
    {
        $this->required = $required;

        return $this;
    }

    public function nullable(?bool $nullable = true)
    {
        $this->required($nullable);

        return $this;
    }

    public function createOpposite(): AttributeBlueprint
    {
        if ($this->action == ActionCase::Create) {
            $attribute = new self(ActionCase::Remove);
            $attribute->name($this->name);
            $attribute->type($this->type);
        }

        if ($this->action == ActionCase::Remove) {
            $attribute = clone $this;
            $attribute->action(ActionCase::Create);
        }

        return $attribute;
    }

    public function modelUp($file)
    {
        // Only add fillable in the case fillable is already present
        if ($this->fillable) {
            $file->add($this->name)->to()->protected()->property('fillable');
        }

        // If it's not fillable, add to the guarded
        if (!$this->fillable) {
            $file->add($this->name)->to()->protected()->property('guarded');
        }

        if ($this->type !== null) {
            $file->add([$this->name => $this->type])->to()->protected()->property("casts");
        }
    }

    public function modelDown($file)
    {
        if (is_array($file->property('fillable'))) {
            $file->protected()->property('fillable', array_diff($file->property('fillable'), [$this->name]));
        }

        if (is_array($file->property('guarded'))) {
            $file->protected()->property('guarded', array_diff($file->property('guarded'), [$this->name]));
        }

        if (is_array($file->property('casts'))) {
            $casts = $file->property('casts');
            unset($casts[$this->name]);

            $file->protected()->property('casts', $casts);
        }
    }

    public function migrateDrop()
    {
        return "->dropColumn('{$this->name}')";
    }

    public function migrateChange()
    {
        return "->change()";
    }

    public function migrateNullable()
    {
        return "->nullable()";
    }

    public function migrateColumn()
    {
        return "->{$this->type}('{$this->name}')";
    }

    public function migrate(): string
    {
        $migration = "";

        if (in_array($this->action, [ActionCase::Remove])) {
            $migration .= $this->migrateDrop();
        }

        if (in_array($this->action, [ActionCase::Create, ActionCase::Update])) {
            $migration .= $this->migrateColumn();

            if ($this->required === false) {
                $migration .= $this->migrateNullable();
            }
        }

        if (in_array($this->action, [ActionCase::Update])) {
            $migration .= $this->migrateChange();
        }

        return $migration.";";
    }

    public function fillFromDatabaseSchema($params) {
        $column = $params->getColumns()->filter(function($column) {
            return $column->getName() == $this->name;
        })->first();

        if (empty($column)) {
            throw new \Exception(sprintf("Couldn't find the attribute in the db %s", $this->name));
        }

        $indexes = $params->getIndexes()->filter(function($index) {
            return in_array($this->name, $index->getColumns()) && count($index->getColumns()) > 1;
        });

        if (count($indexes) > 0) {
            throw new \Exception(sprintf("Please change your index before removing the attribute"));
        }

        $this->guessType($column, $params);
        $this->required($column->isNotNull());
    }

    public function guessType($column, $params)
    {   
        if ($column->getName() == "id") {

            // Check that field is has an idex primary
            $id = $params->getIndexes()->filter(function($index) {
                return in_array($this->name, $index->getColumns()) && count($index->getColumns()) == 1 && $index->getType() == IndexType::PRIMARY;
            })->first();

            if (!empty($id)) {
                return $this->type("id");
            }
        }

        return $this->type($column->getType()->value);
    }

}
