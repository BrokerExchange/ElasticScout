<?php

namespace ElasticScout;

use ElasticScout\Generators\Agg;
use ElasticScout\Generators\DSL;

/**
 * Class Searchable
 * @package ElasticScout
 */
trait Searchable
{
    use \Laravel\Scout\Searchable;

    /**
     * Elasticsearch type to be used within Elasticsearch index
     *
     * @return string
     */
    public function SearchableType()
    {
        return 'doc';
    }

    /**
     * return a fresh instance of agg
     * @return Agg
     */
    public function agg()
    {
        return new Agg;
    }

    /**
     * return a fresh instance of dsl
     *
     * @return DSL
     */
    public function dsl()
    {
        return new DSL;
    }

    /**
     * Perform a search against the model's indexed data.
     *
     * @param  string  $query
     * @param  \Closure  $callback
     * @return Builder
     */
    public static function search($query = null, $callback = null)
    {
        return new Builder(new static, $query, $callback);
    }

}
