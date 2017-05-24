<?php
/**
 * Created by PhpStorm.
 * User: bmix
 * Date: 11/3/16
 * Time: 6:28 AM
 */

namespace ElasticScout;

use Illuminate\Support\ServiceProvider;
use Laravel\Scout\EngineManager;
use ElasticScout\Generators\DSL;
use ElasticScout\Generators\Agg;
use Elasticsearch;

class ElasticScoutServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        resolve(EngineManager::class)->extend('elastic', function () {
            $client =  Elasticsearch\ClientBuilder::create()->setHosts(config('scout.elastic.hosts',['localhost:9200']))->build();
            return new ElasticEngine($client);
        });

        app()->singleton('ElasticScout\Generators\DSL',function() {
            return new DSL;
        });

        app()->singleton('ElasticScout\Generators\Agg',function() {
            return new Agg;
        });

    }
}
