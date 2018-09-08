<?php
namespace App\Packages\Graph\Neo4j;
use App\Packages\Graph\IGraph;
use GraphAware\Neo4j\Client\ClientBuilder;
class Neo4j implements IGraph {
    protected $client;
    protected $username;
    protected $host;
    protected $port;
    protected $password;
    protected $connection;

    public $where = null;
    public $query;
    public $nodes;
    public $nodesWithLabels;
    public $edge;


    public function __construct()
    {
        $this->username = config('database.connections.neo4j.username');
        $this->host = config('database.connections.neo4j.host');
        $this->port = config('database.connections.neo4j.port');
        $this->password = config('database.connections.neo4j.password');
        $this->connection = 'http://'.$this->username.':'.$this->password.'@'.$this->host.':'.$this->port;
        $this->client = ClientBuilder::create()->addConnection('default', $this->connection)->build();
    }


    public $match;

    public function query(){
        $this->query .= " ".$this->edge;
        $this->query .= " ".$this->where;

        return $this->query;
    }

    public function match(array $nodes){
        $query = "MATCH ";
        $nodes_next = $nodes;
        foreach ($nodes as $node => $details) {
            $this->nodes .= $node;
            $properties = "";
            if(isset($details[1])){
                $properties = json_encode($details[1]);
                $properties = preg_replace('/"([a-zA-Z]+[a-zA-Z0-9_]*)":/','$1:',$properties);
            }
            $query .= "($node:$details[0] $properties)";
            if(next($nodes_next)){
                $query .= ",";
                $this->nodes .= ",";
            }
        }

        $this->query = $query;
        return $this;
    }

    public function where($statements){
        $this->where .= $this->where ? " and " : "where ";
        $arr = $statements;
        foreach ($statements as $key => $value){
            $this->where .= "$key = \"$value\"";
            if(next($arr)){
                $this->where .= " and ";
            }
        }
        return $this;
    }

    public function orWhere($statements){
        $this->where .= !is_null($this->where) ? " or " : "where ";
        $statements_next = $statements;
        $query = "";
        foreach ($statements as $key => $value){
            $query .= "$key = \"$value\"";
            if(next($statements_next)){
                $query .= " or ";
            }
        }
        $this->where .= $query;
        return $this;
    }


    public function get(){
        $records = $this->run()->getRecords();
        return $records;
    }

    public function first(){
        $record = $this->run()->getRecord();
        return $record;
    }
    public function run(){
        $query = $this->query();
        $query .= " return $this->nodes";
        $run = $this->client->run($query);
        return $run;
    }


    public function edge(array $query){
        foreach($query as $node => $properties){
            $this->edge .= ",";
            $this->edge .= "($node)";
            if($properties[0] == 'out'){
                $this->edge .= "-[:$properties[1]]->";
            }elseif($properties[0] == 'in'){
                $this->edge .= "<-[:$properties[1]]-";
            }
            $subedge = $properties[array_keys($properties)[2]];
            if(is_array($subedge)){
                $this->edge .= "(".array_keys($properties)[2].")";
                $this->subEdge($properties[array_keys($properties)[2]]);
            }else{
                $this->edge .= "(".$properties[array_keys($properties)[2]].")";
            }
        }
        return $this;
    }
    public function subEdge(array $properties){
        if($properties[0] == 'out'){
            $this->edge .= "-[:$properties[1]]->";
        }elseif($properties[0] == 'in'){
            $this->edge .= "<-[:$properties[1]]-";
        }
        $subedge = $properties[array_keys($properties)[2]];
        if(is_array($subedge)){
            $this->edge .= "(".array_keys($properties)[2].")";
            $this->subEdge($properties[array_keys($properties)[2]]);
        }else{
            $this->edge .= "(".$properties[array_keys($properties)[2]].")";
        }
        return $this;
    }

    public function whereQueryFormatter(){
        $this->where = preg_replace('/"([a-zA-Z]+[a-zA-Z0-9_]*)":/','$1:',$this->where);
        return $this;
    }


    public function create(array $records){
        $query = "create ";
        $nodes = $records['nodes'] ?? [];
        $nodes_next = $nodes;
        foreach ($nodes as $node => $properties){
            $query .= "(".$node;
            $query .= " {";
            $properties_next = $properties;
            foreach ($properties as $property => $value) {
                $query .= "$property : \"$value\"";
                if(next($properties_next)){
                    $query.= ",";
                }
            }
            $query .= "}";
            $query .= ")";
            if(next($nodes_next)){
                $query .= ",";
            }
        }


        $edges = $records['edges'] ?? [];
        $edges_next = $edges;

        $edges_query = $edges == [] ? "" : ", ";
        if(!isset($records['nodes']))
            $edges_query = " create ";

        foreach ($edges as $node => $edge){
            $edge[4] = $node;
            $edges_query .= $this->createEdge($edge);
            if(next($edges_next)){
                $edges_query .= ",";
            }
        }

        $this->query .= $edges_query;


        return $this;
    }

    protected function createEdge(array $edge){
        $from = $edge[4];
        $label = $edge[1];
        $to = $edge[2];
        $direction = $edge[0];
        $properties = isset($edge['properties']) ? isset($edge['properties']) : "";
        $query = "";
        if($properties != "") {
            $properties = json_encode($properties);
            $properties = preg_replace('/"([a-zA-Z]+[a-zA-Z0-9_]*)":/', '$1:', $properties);
        }
        if($direction == "out"){
            $query = "(".$from.")-[:".$label." $properties]->(".$to.")";
        }elseif($direction == "in"){
            $query = "(".$from.")<-[:".$label." $properties]-(".$to.")";
        }

        return $query;
    }




}
