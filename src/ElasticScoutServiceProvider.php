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
            $client =  Elasticsearch\ClientBuilder::create()->setHosts(config('scout.elasticsearch.config.hosts'))->build();
            return new ElasticEngine($client);
        });
    }
}
