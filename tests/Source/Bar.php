<?php

namespace Tests\Generated;

use Illuminate\Database\Eloquent\Model;

class Bar extends Model
{
    protected $fillable = [
        'name'
    ];

    protected $casts = [
        'name' => 'string',
    ];
}
