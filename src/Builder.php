<?php
/**
 * Created by PhpStorm.
 * User: bmix
 * Date: 5/12/17
 * Time: 12:05 PM
 */

namespace ElasticScout;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

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
     * @param null $perPage
     * @param string $pageName
     * @param null $page
     * @return LengthAwarePaginator
     */
    public function paginate($perPage = null, $pageName = 'page', $page = null)
    {
        $engine = $this->engine();

        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $perPage = $perPage ?: $this->model->getPerPage();

        $results = Collection::make($engine->map(
            $rawResults = $engine->paginate($this, $perPage, $page), $this->model

        ));

        if(!empty($rawResults['aggregations'])) {
            $this->aggregations = $rawResults['aggregations'];
        }

        $paginator = (new LengthAwarePaginator($results, $engine->getTotalCount($rawResults), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]));

        return $paginator->appends('query', $this->query);
    }

    /**
     * Add an "order" for the search query.
     *
     * @param  string  $column
     * @param  mixed  $direction
     * @return $this
     */
    public function orderBy($column, $direction = 'asc')
    {
        if(is_array($direction)) {
            $this->orders[] = [
                'column' => $column,
                'direction' => $direction,
            ];
        } else {
            $this->orders[] = [
                'column' => $column,
                'direction' => strtolower($direction) == 'asc' ? 'asc' : 'desc',
            ];
        }

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
     * @param null $key
     * @return mixed
     */
    public function aggregation($key = null)
    {
        if(!empty($key) && isset($this->aggregations[$key]) && isset($this->aggregations[$key]['buckets'])) {
            return Collection::make($this->aggregations[$key]['buckets']);
        }

        return null;
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

    /**
     * @param array $query
     * @return $this
     */
    public function query(Array $query)
    {
        if(count($query)) {
            $this->dsl = $query;
        }

        return $this;
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
    public function must(Array $query)
    {
        if(count($query)) {
            $this->dsl[$this->combo]['must'][] = $query;
        }

        return $this;
    }

    /**
     * @param $filter
     * @return $this
     */
    public function filter(Array $filter)
    {
        if(count($filter)) {
            $this->dsl[$this->combo]['filter'][] = $filter;
        }

        return $this;
    }

    /**
     * @param $query
     * @return $this
     */
    public function should(Array $query)
    {
        if(count($query)) {
            $this->dsl[$this->combo]['should'][] = $query;
        }

        return $this;
    }

    /**
     * @param int $boost
     */
    public function dis_max($boost = 0)
    {
        $this->combo = 'dis_max';
        $this->dsl = [$this->combo => ['boost'=>$boost]];
    }

    /**
     * @param array $queries
     * @return $this
     */
    public function queries(Array $queries)
    {
        if(count($queries)) {
            $this->dsl[$this->combo]['queries'] = $queries;
        }

        return $this;
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
     * @param $query
     * @return $this
     */
    public function positive(Array $query)
    {
        if(count($query)) {
            $this->dsl[$this->combo]['positive'][] = $query;
        }

        return $this;
    }

    /**
     * @param $query
     * @return $this
     */
    public function negative(Array $query)
    {
        if(count($query)) {
            $this->dsl[$this->combo]['negative'][] = $query;
        }

        return $this;
    }

    /**
     * @param int $boost
     */
    public function constant_score($boost = 0)
    {
        $this->combo = 'constant_score';
        $this->dsl = [$this->combo => array_filter(['boost'=>$boost])];
    }
}