<?php

namespace App\Packages\Graph;


class Graph{

    protected $graph;

    public function __construct(IGraph $graph)
    {
        $this->graph = $graph;
    }

    public function getPersons(){
        return $this->graph->get();
    }

    public function get(){
        return $this->graph->get();
    }

    public function where($w){
        return $this->graph->where($w);
    }

    public function orWhere($w){
        return $this->graph->orWhere($w);
    }

    public function labels(array $labels){
        return $this->graph->labels($labels);
    }

    public function create(array $query){
        return $this->graph->create($query);
    }

    public function match(array $query){
        return $this->graph->match($query);
    }

    public function delete(){
        return $this->graph->delete();
    }
}
