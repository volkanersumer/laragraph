<?php

namespace App\Packages\Graph\Facades;

use Illuminate\Support\Facades\Facade;

class Graph extends Facade{

    protected static function getFacadeAccessor()
    {
        //parent::getFacadeAccessor(); // TODO: Change the autogenerated stub
        return 'graph';
    }
}
