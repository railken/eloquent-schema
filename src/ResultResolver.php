<?php

namespace Railken\EloquentSchema;

use Illuminate\Support\Collection;

class ResultResolver extends Collection
{
    public function run(): Collection
    {
        $result = new Collection;

        foreach ($this->items as $key => $item) {
            $item->run();
            $result[$key] = Collection::make($item->getResult());
        }

        return $result;
    }
}
