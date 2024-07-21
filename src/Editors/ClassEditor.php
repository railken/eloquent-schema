<?php

namespace Railken\EloquentSchema\Editors;

use Archetype\Facades\PHPFile;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use Railken\EloquentSchema\Injectors\MethodInjector;
use Railken\EloquentSchema\Support;
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

    public function addPropertyValue(string $name, mixed $value, Visibility $visibility): ClassEditor
    {
        $builder = $this->file->add($value)->to();

        switch ($visibility) {
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

    public function getAttribute(string $name)
    {
        return $this->file->property($name);
    }

    public function getAttributeByIndex(string $name)
    {
        return $this->file->property($name);
    }

    public function isValueInAttribute(string $name, mixed $value): bool
    {
        return in_array($value, $this->getAttribute($name));
    }

    public function addPublicAttribute(string $name, mixed $value): ClassEditor
    {
        $this->addPropertyValue($name, $value, Visibility::Public);

        return $this;
    }

    public function addProtectedProperty(string $name, mixed $value): ClassEditor
    {
        $this->addPropertyValue($name, $value, Visibility::Protected);

        return $this;
    }

    public function addPrivateProperty(string $name, mixed $value): ClassEditor
    {
        $this->addPropertyValue($name, $value, Visibility::Private);

        return $this;
    }

    public function removePropertyValue(string $name, mixed $value): ClassEditor
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
        $traverser->traverse(PHPFile::load(Support::getPathByClass($injector->getRepositoryClassName()))->ast());

        $this->addNodeToBody($injector->getNode());
    }

    public function addNodeToBody(Node $node): array
    {
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new AppendToClassVisitor($node));

        return $traverser->traverse([$this->file->ast()[0]]);
    }
}
