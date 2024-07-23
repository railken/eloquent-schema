<?php

namespace Railken\EloquentSchema\Exceptions;

class ClassAlreadyExistsException extends Exception
{
    public function __construct(string $model, string $path)
    {
        $message = sprintf('Impossibile create model: %s, in path: %s because the file already exists', $model, $path);
        parent::__construct($message);
    }
}
