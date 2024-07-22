<?php

namespace Railken\EloquentSchema\Hooks;

trait HookManager
{
    protected static array $hooks = [];

    public static function callHooks(string $event, array $params): void
    {
        foreach (self::getHooks() as $hook) {
            $hook = new $hook();
            $hook->$event(...$params);
        }
    }

    public static function getHooks(): array
    {
        return self::$hooks;
    }

    public static function setHooks(array $hooks): void
    {
        self::$hooks = $hooks;
    }

    public static function addHooks(array $hooks): void
    {
        self::setHooks(array_merge(self::$hooks, $hooks));
    }
}
