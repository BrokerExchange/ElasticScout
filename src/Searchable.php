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
        return '_doc';
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
