<?php

namespace Railken\EloquentSchema\Actions\Migration;

use Illuminate\Support\Collection;
use Railken\EloquentSchema\Blueprints\AttributeBlueprint;

interface ColumnActionHookContract
{
    public function migrate(Collection $changes, ?AttributeBlueprint $oldAttribute, AttributeBlueprint $newAttribute);

    public function updateBlueprintFromDatabase(AttributeBlueprint $attributeBlueprint, $column, $params);
}
