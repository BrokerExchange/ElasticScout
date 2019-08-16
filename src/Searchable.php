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
    public static function search($query = '', $callback = null)
    {
        if(empty($query)) {
            $query = request('query');
        }
        return new Builder(
            new static, $query, $callback, static::usesSoftDelete() && config('scout.soft_delete', false)
        );
    }

    /**
     * Retrieve hilighted field result as a collection
     *
     * @param $field
     * @return \Illuminate\Support\Collection
     */
    public function highlight($field)
    {
        if(!empty($this->attributes['highlight'][$field])) {
            return collect($this->attributes['highlight'][$field]);
        }

        return collect();
    }

}
