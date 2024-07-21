<?php

namespace Tests\Generated;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
class Baz extends Model
{
    protected $fillable = [
        'name',
        'summary',
    ];

    protected $casts = [
        'name' => 'string',
        'summary' => 'string',
    ];
    protected function description() : Attribute
    {
        return Attribute::make(get: fn(?string $value) => $this->summary, set: fn(?string $value) => $this->summary = $value);
    }
}
