<?php

namespace Tests\Generated;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Bar extends Model
{
    protected $fillable = [
        'name'
    ];

    protected $casts = [
        'name' => 'string',
    ];

    protected function oldName(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value, $attributes) => $attributes['name'],
            set: fn (?string $value, $attributes) => ['name' => $value],
        );
    }
}
