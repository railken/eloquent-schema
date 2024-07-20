<?php

namespace Railken\EloquentSchema;

use Archetype\Facades\PHPFile;

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

    public function save()
    {
        file_put_contents($this->getPath(), $this->render());
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
}
