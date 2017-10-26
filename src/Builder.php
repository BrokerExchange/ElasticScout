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
     * holds the raw query language
     *
     * @var array
     */
    protected $dsl = [];

    /**
     * holds raw query aggregations
     * @var array
     */
    protected $aggregations = [];

    /**
     * holds raw post filters
     * @var array
     */
    protected $post_filter = [];

    /**
     * holds raw highlights
     * @var array
     */
    protected $highlights = [];

    /**
     * the specified combo query
     *
     * @var string
     */
    protected $combo = '';

    /**
     * execute query and paginate results
     *
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

        $paginator = (new LengthAwarePaginator($results, $engine->getTotalCount($rawResults), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]));

        //we append either $this->query, request('query'), or request('q') if set
        if(!empty($this->query)) {

            $paginator->appends('query',$this->query);

        } elseif (request()->has('query') && !empty(request('query'))) {

            $paginator->appends('query',request('query'));

        } elseif (request()->has('q') && !empty(request('q'))) {

            $paginator->appends('q',request('q'));

        }

        //we append either request('filter') or request('f') if set
        if(request()->has('filter') && !empty(request('filter'))) {

            $paginator->appends('filter',request('filter'));

        } elseif (request()->has('f') && !empty(request('f'))) {

            $paginator->appends('f',request('f'));

        }

        return $paginator;
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
     * return raw query dsl
     *
     * @return mixed
     */
    public function dsl()
    {
        return array_filter([
            'query' => $this->dsl,
            'aggregations' => $this->aggregations,
            'post_filter' => $this->post_filter,
            'highlight' => $this->highlights,
        ]);
    }

    /**
     * retrieve aggregation result
     *
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
     * set aggregations
     *
     * @param array $aggregations
     */
    public function aggregations(Array $aggregations)
    {
        $this->aggregations = $aggregations;
    }

    /**
     * add aggregation to query
     *
     * @param $agg
     * @return $this
     */
    public function aggregate(Array $agg)
    {
        $this->aggregations = array_merge($this->aggregations,$agg);
        return $this;
    }

    /**
     * return query DSL as json
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->dsl());
    }

    /**
     * set query raw query dsl
     *
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
     * add boolean combo query
     *
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
     * add must to combo query
     *
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
     * add filter to combo query
     *
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
     * add filter to post filters
     *
     * @param array $filter
     * @return $this
     */
    public function post_filter(Array $filter)
    {
        if(count($filter)) {
            $this->post_filter = array_merge($this->post_filter,$filter);
        }

        return $this;
    }

    /**
     * add highlight to query
     *
     * @param array $fields
     * @return $this
     */
    public function highlight(Array $fields)
    {
        if(count($fields)) {
            $this->highlights = array_merge($this->highlights,$fields);
        }

        return $this;
    }

    /**
     * get highlights
     *
     * @return array
     */
    public function highlights()
    {
        return $this->highlights;
    }

    /**
     * add should to combo query
     *
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
     * add dis_max combo query
     *
     * @param int $boost
     */
    public function dis_max($boost = 0)
    {
        $this->combo = 'dis_max';
        $this->dsl = [$this->combo => ['boost'=>$boost]];
    }

    /**
     * add queries to combo query
     *
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
     * add boosting combo query
     *
     * @param int $negative_boost
     */
    public function boosting($negative_boost = 0)
    {
        $this->combo = 'boosting';
        $this->dsl = [$this->combo => array_filter(['negative_boost' => $negative_boost])];
    }

    /**
     * add positive to combo query
     *
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
     * add negative to combo query
     *
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
     * add constant_score combo query
     *
     * @param int $boost
     */
    public function constant_score($boost = 0)
    {
        $this->combo = 'constant_score';
        $this->dsl = [$this->combo => array_filter(['boost'=>$boost])];
    }
}