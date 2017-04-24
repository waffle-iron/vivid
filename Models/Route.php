<?php

use Database\Vivid\Model;

class Route extends Model
{
    protected $table = 'C_1001_1.Routes';
    protected $relations = [
        'belongs-to' => [],
        'belongs-to-many' => [],
        'has-one' => [
            'page' => File::class,
        ],
        'has-many' => [],
    ];

    public function doSomething(string $message)
    {
        return $message;
    }
}
