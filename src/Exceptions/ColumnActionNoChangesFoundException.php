<?php

namespace Railken\EloquentSchema\Exceptions;

class ColumnActionNoChangesFoundException extends Exception
{
    public function __construct(string $attributeName, string $modelName)
    {
        $message = sprintf('No changes found for the migration of attribute %s in model %s', $attributeName, $modelName);
        parent::__construct($message);
    }
}
