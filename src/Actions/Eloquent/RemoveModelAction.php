<?php

namespace Railken\EloquentSchema\Actions\Eloquent;

class RemoveModelAction extends CreateModelAction
{
    public function run(): void
    {
        $this->remove($this->model);
        $this->save();
    }
}
