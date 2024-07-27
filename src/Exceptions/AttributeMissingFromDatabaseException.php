<?php

namespace Railken\EloquentSchema\Exceptions;

class AttributeMissingFromDatabaseException extends Exception
{
    public function __construct(string $columnName, string $columnType)
    {
        $message = sprintf('Couldn\'t find any attributes named %s for the value %s', $columnName, $columnType);
        parent::__construct($message);
    }
}
