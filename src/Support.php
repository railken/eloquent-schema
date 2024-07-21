<?php

namespace Railken\EloquentSchema;

use ReflectionException;
use ReflectionClass;

class Support
{
    /**
     * @throws ReflectionException
     */
    public static function getPathByClass(string $class): string
    {
        $reflector = new ReflectionClass($class);
        return $reflector->getFileName();
    }

    /**
     * @throws ReflectionException
     */
    public static function getPathByObject(mixed $object): string
    {
        return self::getPathByClass(get_class($object));
    }
}
