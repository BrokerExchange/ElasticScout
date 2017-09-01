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
        $this->mergeConfigFrom(
            __DIR__.'/../config/elastic.php', 'scout.elasatic'
        );

        resolve(EngineManager::class)->extend('elastic', function () {
            $client =  Elasticsearch\ClientBuilder::create()->setHosts(config('elastic.hosts',['localhost:9200']))->build();
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

        $this->publishes([
            __DIR__.'/../config/elastic.php' => $this->app['path.config'].DIRECTORY_SEPARATOR.'elastic.php',
        ]);


    }
}
