<?php
/**
 * Created by PhpStorm.
 * User: volka
 * Date: 4.09.2018
 * Time: 15:01
 */

namespace App\Packages\Graph;


interface IGraph
{
    public function match(array $query);
    public function get();
    public function where($query);
    public function orWhere($query);
}
