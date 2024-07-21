<?php

namespace Tests\Generated;

use Illuminate\Database\Eloquent\Model;

class Baz extends Model
{
    protected $fillable = [
        'name', 'description',
    ];

    protected $casts = [
        'name' => 'string',
        'description' => 'string',
    ];
}
