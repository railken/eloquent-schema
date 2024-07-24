<?php

namespace Railken\EloquentSchema\Exceptions;

class AttributeNotFoundException extends Exception
{
    public function __construct(string $attributeName, string $modelName)
    {
        $message = sprintf('The attribute %s doesn\'t exists in the model: %s', $attributeName, $modelName);
        parent::__construct($message);
    }
}
