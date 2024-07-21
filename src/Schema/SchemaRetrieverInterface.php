<?php

namespace Railken\EloquentSchema\Schema;

use Illuminate\Support\Collection;

interface SchemaRetrieverInterface
{
    public function retrieveAttributes(string $table): Collection;

    public function getModels(): Collection;
}
