<?php
/**
 * Created by PhpStorm.
 * User: bmix
 * Date: 5/12/17
 * Time: 12:05 PM
 */

namespace ElasticScout;

/**
 * Class Builder
 * @package ElasticScout
 */
class Builder extends \Laravel\Scout\Builder
{
    /**
     * @var array
     */
    protected $dsl = [];

    /**
     * @var array
     */
    protected $aggregations = [];

    /**
     * @var string
     */
    protected $combo = '';

    /**
     * @param int $boost
     */
    public function dis_max($boost = 0)
    {
        $this->combo = 'dis_max';
        $this->dsl = [$this->combo => ['boost'=>$boost]];
    }

    /**
     * @param int $negative_boost
     */
    public function boosting($negative_boost = 0)
    {
        $this->combo = 'boosting';
        $this->dsl = [$this->combo => array_filter(['negative_boost' => $negative_boost])];
    }

    /**
     * @param int $boost
     */
    public function constant_score($boost = 0)
    {
        $this->combo = 'constant_score';
        $this->dsl = [$this->combo => array_filter(['boost'=>$boost])];
    }

    /**
     * @param int $boost
     * @param int $minimum_should_match
     * @return $this
     */
    public function boolean($boost=0, $minimum_should_match=0)
    {
        $this->combo = 'bool';
        $this->dsl = [$this->combo => array_filter(['boost'=>$boost,'minimum_should_match'=>$minimum_should_match])];
        return $this;
    }

    /**
     * @param $query
     * @return $this
     */
    public function positive($query)
    {
        $this->dsl[$this->combo]['positive'][] = $query;
        return $this;
    }

    /**
     * @param $query
     * @return $this
     */
    public function negative($query)
    {
        $this->dsl[$this->combo]['negative'][] = $query;
        return $this;
    }

    /**
     * @param array $queries
     */
    public function queries(Array $queries)
    {
        $this->dsl[$this->combo]['queries'] = $queries;
    }

    /**
     * @param $query
     * @return $this
     */
    public function must($query)
    {
        $this->dsl[$this->combo]['must'][] = $query;
        return $this;
    }

    /**
     * @param $filter
     * @return $this
     */
    public function filter($filter)
    {
        $this->dsl[$this->combo]['filter'][] = $filter;
        return $this;
    }

    /**
     * @param $query
     * @return $this
     */
    public function should($query)
    {
        $this->dsl[$this->combo]['should'][] = $query;
        return $this;
    }

    /**
     * @return mixed
     */
    public function dsl()
    {
        return array_filter([
            'query' => $this->dsl,
            'aggregations' => $this->aggregations,
        ]);
    }

    /**
     * @return mixed
     */
    public function aggregations()
    {
        return $this->aggregations;
    }

    /**
     * @param $agg
     * @return $this
     */
    public function aggregate(Array $agg)
    {
        $this->aggregations = array_merge($this->aggregations,$agg);
        return $this;
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->dsl());
    }
}