<?php

use Database\Vivid\Model;

class File extends Model
{
    protected $table = 'C_1001_1.Files';
    protected $relations = [
        'belongs-to' => [],
        'belongs-to-many' => [
            Route::class,
        ],
        'has-one' => [],
        'has-many' => [],
    ];
}
