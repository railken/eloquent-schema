<?php

namespace Railken\EloquentSchema\Actions\Eloquent;
use Illuminate\Support\Str;
use Railken\EloquentSchema\Injectors\ModelMutatorRenameInjector;

class RenameAttributeAction extends UpdateAttributeAction
{
    public function run(): void
    {
        parent::run();

        $this->classEditor->addUse(
            \Illuminate\Database\Eloquent\Casts\Attribute::class
        );
        $this->save();

        // reload file...
        $this->classEditor->reload();

        $injector = new ModelMutatorRenameInjector(Str::camel($this->oldAttribute->name), $this->newAttribute->name);
        $this->classEditor->inject($injector);
        $this->save();
    }
}
