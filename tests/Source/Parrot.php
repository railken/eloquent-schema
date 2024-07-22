<?php

use Illuminate\Database\Eloquent\Model;

return new class extends Model
{
    protected $table = 'parrots';

    protected $fillable = [
        'name',
    ];

    protected $casts = [
        'name' => 'string',
    ];
};
