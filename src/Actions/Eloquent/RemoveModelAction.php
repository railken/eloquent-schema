<?php

namespace Railken\EloquentSchema\Actions\Eloquent;

class RemoveModelAction extends CreateModelAction
{
    public function run(): void
    {
        unlink($this->classEditor->getPath());

        $this->result = [$this->classEditor->getPath() => null];
    }
}
