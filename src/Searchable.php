<?php

namespace ElasticScout;

/**
 * Class Searchable
 * @package ElasticScout
 */
trait Searchable
{
    use DSL;

    use \Laravel\Scout\Searchable;

    /**
     * @return string
     */
    public function SearchableType()
    {
        return 'doc';
    }

    /**
     * @return Aggregation
     */
    public function agg()
    {
        return new Aggregation;
    }

    /**
     * Perform a search against the model's indexed data.
     *
     * @param  string  $query
     * @param  Closure  $callback
     * @return \Laravel\Scout\Builder
     */
    public static function search($query = null, $callback = null)
    {
        return new Builder(new static, $query, $callback);
    }
}