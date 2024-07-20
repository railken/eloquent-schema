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

        $injector = new ModelMutatorRenameInjector(Str::camel($this->oldAttribute->name), $this->newAttribute->name);
        $this->classEditor->inject($injector);


        /*$this->classEditor
            ->addProtectedMethod(Str::camel($this->oldAttribute))
            ->setReturnType(Attribute::class)
            ->setBody(function() {
                return Attribute::make(
                    get: fn (string $value) => ucfirst($value),
                    set: fn (string $value) => strtolower($value),
                );
            });

        // add legacy in case there is code needed
        protected function firstName(): Attribute
        {
            return Attribute::make(
                get: fn (string $value) => ucfirst($value),
                set: fn (string $value) => strtolower($value),
            );
        }*/
    }
}
