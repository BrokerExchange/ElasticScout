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

	public $highlight;
    /**
     * @var array
     */
    protected $sorting = [];

    /**
     * @return string
     */
    public function SearchableType()
    {
        return 'doc';
    }

    /**
     * @return Agg
     */
    public function agg()
    {
        return new Agg;
    }

    /**
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
     * @return \Laravel\Scout\Builder
     */
    public static function search($query = null, $callback = null)
    {
        return new Builder(new static, $query, $callback);
    }

    /**
     * @param $sort
     */
    public function addSorting($sort)
    {
        $this->sorting = $sort;
    }

    /**
     * @return array
     */
    public function sorting()
    {
        return $this->sorting;
    }
}
