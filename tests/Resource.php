<?php

namespace Tests;

#[\Attribute]
class Resource
{
    public string $path;
    public function __construct(string $path)
    {
        $this->path = $path;
    }
}
