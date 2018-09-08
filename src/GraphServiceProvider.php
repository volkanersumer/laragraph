<?php

namespace App\Packages\Graph;

use Illuminate\Support\ServiceProvider;

class GraphServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        app()->bind('graph', function(){
            return new Graph(new Neo4j\Neo4j());
        });



    }
}
