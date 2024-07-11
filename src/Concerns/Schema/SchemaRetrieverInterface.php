<?php

namespace Railken\EloquentSchema\Concerns\Schema;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

interface SchemaRetrieverInterface
{
    public function retrieveAttributes(string $table): Collection;
    public function getModels(): Collection;
}