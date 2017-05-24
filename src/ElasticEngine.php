<?php
/**
 * Created by PhpStorm.
 * User: bmix
 * Date: 11/3/16
 * Time: 6:28 AM
 */

namespace ElasticScout;

use Elasticsearch\Client as Elasticsearch;
use Elasticsearch\Common\Exceptions\ClientErrorResponseException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

/**
 * Class ElasticEngine
 * @package ElasticScout
 */
class ElasticEngine
{
    /**
     * The Elasticsearch client instance.
     *
     * @var \Elasticsearch\Client
     */
    protected $elasticsearch;

    /**
     * ElasticEngine constructor.
     * @param Elasticsearch $elasticsearch
     */
    public function __construct(Elasticsearch $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    /**
     * Update the given model in the index.
     *
     * @param  Collection  $models
     * @return void
     */
    public function update($models)
    {
        $body = new BaseCollection();

        $models->each(function ($model) use ($body) {
            $array = $model->toSearchableArray();

            if (empty($array)) {
                return;
            }

            $body->push([
                'index' => [
                    '_index' => $model->searchableAs(),
                    '_type' => $model->searchableType(),
                    '_id' => $model->getKey(),
                ],
            ]);

            $body->push($array);

            if($this->indexExistsFor($model) === false && (method_exists($model, 'settings') || method_exists($model, 'aliases') || method_exists($model, 'mappings'))) {
                $this->createIndex($model);
            }

        });

        $this->elasticsearch->bulk([
            'refresh' => true,
            'body' => $body->all(),
        ]);
    }

    /**
     * Check to see if the index exists
     *
     * @param $model
     * @return bool
     */
    protected function indexExistsFor($model)
    {
        $params['index'] = $model->searchableAs();
        return $this->elasticsearch->indices()->exists($params);
    }

    /**
     * Create the index, putting mappings, settings, and aliases
     *
     * @param $model
     * @return bool
     * @throws ClientErrorResponseException
     */
    protected function createIndex($model)
    {
        $params['index'] = $model->searchableAs();

        if(method_exists($model,'settings')) {
            $params['body']['settings'] = $model->settings();
        }

        if(method_exists($model,'mappings')) {
            $params['body']['mappings'] = $model->mappings();
        }

        if(method_exists($model, 'aliases')) {
            $params['body']['aliases'] = $model->aliases();
        }

        $response = $this->elasticsearch->indices()->create($params);

        if(!empty($response['acknowledged'])) {
            return true;
        }

        throw new ClientErrorResponseException('Failed to create index for an unknown reason');
    }

    /**
     * Remove the given model from the index.
     *
     * @param  Collection  $models
     * @return void
     */
    public function delete($models)
    {
        $body = new BaseCollection();

        $models->each(function ($model) use ($body) {
            $body->push([
                'delete' => [
                    '_index' => $model->searchableAs(),
                    '_type' => $model->searchableType(),
                    '_id'  => $model->getKey(),
                ],
            ]);
        });

        $this->elasticsearch->bulk([
            'refresh' => true,
            'body' => $body->all(),
        ]);
    }

    /**
     * Perform the given search on the engine.
     *
     * @param Builder $query
     * @return array
     */
    public function search(Builder $query)
    {
        return $this->performSearch($query, [
            'orders' => $this->orders($query),
            'size' => $query->limit ?: 10000,
        ]);
    }

    /**
     * Perform the given search on the engine.
     *
     * @param Builder $query
     * @param int $perPage
     * @param int $page
     * @return mixed
     */
    public function paginate(Builder $query, $perPage, $page)
    {
        $result = $this->performSearch($query, [
            'orders' => $this->orders($query),
            'size' => $perPage,
            'from' => (($page * $perPage) - $perPage),
        ]);

        $result['nbPages'] = (int) ceil($result['hits']['total'] / $perPage);

        return $result;
    }

    /**
     * Perform the given search on the engine.
     *
     * @param Builder $query
     * @param array $options
     * @return array
     */
    protected function performSearch(Builder $query, array $options = [])
    {
        $search = [
            'index' =>  !empty($query->index)?$query->index:$query->model->searchableAs(),
            'type'  =>  $query->model->searchableType(),
            'body' => $query->dsl(),
        ];

        if (array_key_exists('size', $options)) {
            $search = array_merge($search, [
                'size' => $options['size'],
            ]);
        }

        if (array_key_exists('orders', $options)) {
            $search['body'] = array_merge($search['body'], [
                'sort' => $options['orders'],
            ]);
        }

        if (array_key_exists('from', $options)) {
            $search = array_merge($search, [
                'from' => $options['from'],
            ]);
        }

//        dd($search);

        return $this->elasticsearch->search($search);
    }

    /**
     * @param Builder $query
     * @return array
     */
    protected function orders(Builder $query)
    {
        return collect($query->orders)->mapWithKeys(function($sort) {
            return [$sort['column'] => $sort['direction']];
        })->all();
    }

    /**
     * Pluck and return the primary keys of the given results.
     *
     * @param  mixed  $results
     * @return \Illuminate\Support\Collection
     */
    public function mapIds($results)
    {
        return collect($results['hits'])->pluck('_id')->values();
    }

    /**
     * Map the given results to instances of the given model.
     *
     * @param  mixed  $results
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Support\Collection;
     */
    public function map($results, $model)
    {
        if (count($results['hits']) === 0) {
            return Collection::make();
        }

        $keys = collect($results['hits']['hits'])
            ->pluck('_id')
            ->values()
            ->all();

        $models = $model->whereIn(
            $model->getQualifiedKeyName(), $keys
        )->get()->keyBy($model->getKeyName());

        return Collection::make($results['hits']['hits'])->map(function ($hit) use ($model, $models) {

            if(isset($models[$hit['_source'][$model->getKeyName()]])) {

                $newModel = $models[$hit['_source'][$model->getKeyName()]];

                if(!empty($hit['sort'])) {
                    $newModel->addSorting($hit['sort']);
                }

                return $newModel;
            }

            return null;

        })->filter()->values();
    }

    /**
     * Get the total count from a raw result returned by the engine.
     *
     * @param  mixed  $results
     * @return int
     */
    public function getTotalCount($results)
    {
        return $results['hits']['total'];
    }

    /**
     * Get the results of the query as a Collection of primary keys.
     *
     * @param  Builder  $builder
     * @return \Illuminate\Support\Collection
     */
    public function keys(Builder $builder)
    {
        return $this->mapIds($this->search($builder));
    }

    /**
     * Get the results of the given query mapped onto models.
     *
     * @param  Builder  $builder
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get(Builder $builder)
    {
        $results = Collection::make($this->map(
            $rawResults = $this->search($builder), $builder->model
        ));

        if(!empty($rawResults['aggregations'])) {
            $builder->aggs($rawResults['aggregations']);
        }

        return $results;
    }
}
