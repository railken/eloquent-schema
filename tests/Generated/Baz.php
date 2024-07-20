<?php

namespace Tests\Generated;

use Illuminate\Database\Eloquent\Model;

class Baz extends Model
{
    protected $guarded = [
        'summary',
    ];
    protected $fillable = [
        'name',
    ];

    protected $casts = [
        'name' => 'string',
        'summary' => 'string',
    ];
}
