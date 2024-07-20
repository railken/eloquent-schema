<?php

namespace Tests\Generated;

use Illuminate\Database\Eloquent\Model;

class Foo extends Model
{
    protected $casts = [
        'fillable_field' => 'string',
    ];

    protected $fillable = [
        'fillable_field',
    ];
}
