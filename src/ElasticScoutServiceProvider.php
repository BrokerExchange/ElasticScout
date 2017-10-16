<?php

namespace ElasticScout;

use Illuminate\Support\ServiceProvider;
use ElasticScout\Generators\DSL;
use ElasticScout\Generators\Agg;
use Elasticsearch;

/**
 * Class ElasticScoutServiceProvider
 * @package ElasticScout
 */
class ElasticScoutServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/elastic.php', 'scout.elastic'
        );

        resolve(\Laravel\Scout\EngineManager::class)->extend('elastic', function () {
            $client =  Elasticsearch\ClientBuilder::create()->setHosts(config('scout.elastic.hosts', ['localhost:9200']))->build();
            return new ElasticEngine($client);
        });

        app()->bind(\Laravel\Scout\Builder::class, function($model, $query, $callback = null) {
            return new Builder($model, $query, $callback);
        });

        app()->singleton('ElasticScout\Generators\DSL',function() {
            return new DSL;
        });

        app()->singleton('ElasticScout\Generators\Agg',function() {
            return new Agg;
        });
    }
}
