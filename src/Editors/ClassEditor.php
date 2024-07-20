<?php

namespace Railken\EloquentSchema\Editors;

use Archetype\Facades\PHPFile;

use PhpParser\{Node, NodeTraverser, NodeVisitorAbstract, BuilderFactory};
use Railken\EloquentSchema\Injectors\Injector;
use Railken\EloquentSchema\Injectors\MethodInjector;

class ClassEditor
{
    protected string $path;
    protected \Archetype\PHPFile $file;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->file = PHPFile::load($path);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function save(): array
    {
        $render = $this->render();
        file_put_contents($this->getPath(), $render);
        return [$this->getPath() => $render];
    }

    public function render(): string
    {
        return $this->file->render();
    }
    public function addAttribute(string $name, mixed $value, Visibility $visibility): ClassEditor
    {
        $builder = $this->file->add($value)->to();

        switch($visibility) {
            case Visibility::Public:
                $builder = $builder->public();
                break;
            case Visibility::Protected:
                $builder = $builder->protected();
                break;
            case Visibility::Private:
                $builder = $builder->private();
                break;
        }

        $builder->property($name);

        return $this;
    }

    public function addPublicAttribute(string $name, mixed $value): ClassEditor
    {
        $this->addAttribute($name, $value, Visibility::Public);

        return $this;
    }
    public function addProtectedAttribute(string $name, mixed $value): ClassEditor
    {
        $this->addAttribute($name, $value, Visibility::Protected);

        return $this;
    }
    public function addPrivateAttribute(string $name, mixed $value): ClassEditor
    {
        $this->addAttribute($name, $value, Visibility::Private);

        return $this;
    }


    public function removeAttributeValue(string $name, mixed $value): ClassEditor
    {
        if (is_array($this->file->property($name))) {
            $this->file->protected()->property($name, array_diff($this->file->property($name), [$value]));
        }

        return $this;
    }

    public function removeAttributeValueByIndex(string $name, mixed $value): ClassEditor
    {
        if (is_array($this->file->property($name))) {
            $casts = $this->file->property($name);

            unset($casts[$value]);

            $this->file->protected()->property($name, $casts);
        }

        return $this;
    }

    public function addUse(string $class)
    {
        $this->file->use($class);
        return $this;
    }

    public function updateFile($stmts): void
    {
        $query = $this->file->astQuery();
        $query->resultingAST = $stmts;
        $query->commit();
    }

    public function inject(MethodInjector $injector): void
    {
        $prettyPrinter = new \PhpParser\PrettyPrinter\Standard;
        $traverser = new NodeTraverser;
        $traverser->addVisitor($injector);

        $reflector = new \ReflectionClass(get_class($injector));


        $traverser->traverse(PHPFile::load($reflector->getFileName())->ast());

        $stmts = $injector->getStmts();

        $factory = new BuilderFactory;
        $node = $factory->namespace('Temp')
            ->addStmt($factory->class('Temp')
                ->addStmt($stmts[0])
            )->getNode();


        $newCode = $prettyPrinter->prettyPrintFile([$node]);

        // $newCode = $prettyPrinter->prettyPrintFile($stmts);

        $parser = (new \PhpParser\ParserFactory())->createForNewestSupportedVersion();


        $stmts = $parser->parse($newCode);


        $toSave = $this->file->ast();
        $toSave[0]->stmts[1]->stmts[] = $stmts[0]->stmts[0]->stmts[0];


        $this->updateFile($toSave);

    }
}
