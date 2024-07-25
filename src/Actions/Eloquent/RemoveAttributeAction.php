<?php

namespace Railken\EloquentSchema\Actions\Eloquent;

class RemoveAttributeAction extends CreateAttributeAction
{
    public function run(): void
    {
        $this->removeFromModel($this->newAttribute);
        $this->save();
    }
}
