<?php

namespace Railken\EloquentSchema\Editors;

use Archetype\Facades\PHPFile;

use PhpParser\{Node, NodeTraverser, NodeVisitorAbstract, BuilderFactory};
use Railken\EloquentSchema\Injectors\Injector;
use Railken\EloquentSchema\Injectors\MethodInjector;
use Railken\EloquentSchema\Visitors\AppendToClassVisitor;

class ClassEditor
{
    protected string $path;
    protected \Archetype\PHPFile $file;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->load();
    }

    public function load(): void
    {
        $this->file = PHPFile::load($this->path);
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

    public function addUse(string $class): ClassEditor
    {
        $uses = $this->file->use();
        $uses[] = $class;
        $this->file->use($uses);

        return $this;
    }

    public function inject(MethodInjector $injector): void
    {
        $traverser = new NodeTraverser();
        $traverser->addVisitor($injector);

        $reflector = new \ReflectionClass($injector->getRepositoryClassName());
        $traverser->traverse(PHPFile::load($reflector->getFileName())->ast());

        $this->addNodeToBody($injector->getNode());
    }

    public function addNodeToBody(Node $node): array
    {
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new AppendToClassVisitor($node));
        return $traverser->traverse([$this->file->ast()[0]]);
    }
}
