<?php
/**
 * Created by PhpStorm.
 * User: bmix
 * Date: 5/12/17
 * Time: 1:48 PM
 */

namespace ElasticScout\Generators;

/**
 * Class Aggregation
 * @package ElasticScout
 */
class Agg
{

    /**
     * @param $namespace
     * @param $field
     * @param int $size
     * @param array $order
     * @return array
     */
    public function terms($namespace,$field,$size=0,$order=null)
    {
        $terms = array_filter([
            'field' => $field,
            'size' => $size
        ]);

        if(!empty($order))
        {
            array_merge($terms,['order' => $order]);
        }

        return [
            $namespace => [
                'terms' => $terms
            ]
        ];
    }

    /**
     * @param $namespace
     * @param $field
     * @return array
     */
    public function stats($namespace,$field)
    {
        return [
            $namespace => [
                'stats' => [
                    'field' => $field
                ]
            ]
        ];
    }

    /**
     * @param $namespace
     * @param $field
     * @return array
     */
    public function extended_stats($namespace,$field)
    {
        return [
            $namespace => [
                'extended_stats' => [
                    'field' => $field
                ]
            ]
        ];
    }

    /**
     * @param $namespace
     * @param $field
     * @return array
     */
    public function min($namespace,$field)
    {
        return [
            $namespace => [
                'min' => [
                    'field' => $field
                ]
            ]
        ];
    }

    /**
     * @param $namespace
     * @param $field
     * @return array
     */
    public function max($namespace,$field)
    {
        return [
            $namespace => [
                'max' => [
                    'field' => $field
                ]
            ]
        ];
    }

    /**
     * @param $namespace
     * @param $field
     * @return array
     */
    public function avg($namespace,$field)
    {
        return [
            $namespace => [
                'avg' => [
                    'field' => $field
                ]
            ]
        ];
    }

    /**
     * @param $namespace
     * @param $field
     * @return array
     */
    public function cardinality($namespace,$field)
    {
        return [
            $namespace => [
                'cardinality' => [
                    'field' => $field
                ]
            ]
        ];
    }

    /**
     * @param $namespace
     * @param $field
     * @return array
     */
    public function sum($namespace,$field)
    {
        return [
            $namespace => [
                'sum' => [
                    'field' => $field
                ]
            ]
        ];
    }

    /**
     * @param $namespace
     * @param $field
     * @return array
     */
    public function value_count($namespace,$field)
    {
        return [
            $namespace => [
                'value_count' => [
                    'field' => $field
                ]
            ]
        ];
    }

    /**
     * @param $namespace
     * @param $field
     * @param bool $wrap_lon
     * @return array
     */
    public function geo_bounds($namespace,$field,$wrap_lon=true)
    {
        return [
            $namespace => [
                'geo_bounds' => [
                    'field' => $field,
                    'wrap_longitude' => $wrap_lon
                ]
            ]
        ];
    }

    /**
     * @param $namespace
     * @param $filter
     * @return array
     */
    public function filter($namespace,$filter,$aggs)
    {
        return [
            $namespace => [
                'filter' => $filter,
                'aggs' => $aggs
            ]
        ];
    }

    /**
     * @param $namespace
     * @param $filters
     * @param $aggs
     * @return array
     */
    public function filters($namespace,$filters,$aggs)
    {
        return [
            $namespace => [
                'filters' => [
                    'filters' => $filters
                ],
                'aggs' => $aggs
            ]
        ];
    }

    /**
     * @param $namespace
     * @param $field
     * @param $ranges
     * @return array
     */
    public function range($namespace,$field,$ranges)
    {
        return [
            $namespace => [
                'range' => [
                    'field' => $field,
                    'ranges' => $ranges
                ]
            ]
        ];
    }

    /**
     * @param $namespace
     * @param $type
     * @return array
     */
    public function children($namespace,$type)
    {
        return [
            $namespace => [
                'children' => [
                    'type' => $type
                ]
            ]
        ];
    }

    /**
     * @param $namespace
     * @param $field
     * @param $interval
     * @param string $format
     * @param string $timezone
     * @param string $offset
     * @return array
     */
    public function date_histogram($namespace,$field,$interval,$format=null,$timezone=null,$offset=null)
    {
        return [
            $namespace => [
                'date_histogram' => array_filter([
                    'field' => $field,
                    'interval' => $interval,
                    'format' => $format,
                    'timezone' => $timezone,
                    'offset' => $offset
                ])
            ]
        ];
    }

    /**
     * @param $namespace
     * @param $field
     * @param $ranges
     * @param string $format
     * @return array
     */
    public function date_range($namespace,$field,$ranges,$format=null)
    {
        return [
            $namespace => [
                'date_range' => array_filter([
                    'field' => $field,
                    'format' => $format,
                    'ranges' => $ranges
                ])
            ]
        ];
    }

    /**
     * @param $namespace
     * @param $field
     * @param $origin
     * @param $ranges
     * @param string $unit
     * @return array
     */
    public function geo_distance($namespace,$field,$origin,$ranges,$unit=null)
    {
        return [
            $namespace => [
                'geo_distance' => array_filter([
                    'field' => $field,
                    'origin' => $origin,
                    'unit' => $unit,
                    'ranges' => $ranges
                ])
            ]
        ];
    }

    /**
     * @param $namespace
     * @param $field
     * @param int $percision
     * @param int $size
     * @param int $shard_size
     * @return array
     */
    public function geohash_grid($namespace,$field,$percision=2,$size=10000,$shard_size=0)
    {
        return [
            $namespace => [
                'geohash_grid' => [
                    'field' => $field,
                    'percision' => $percision,
                    'size' => $size,
                    'shard_size' => $shard_size
                ]
            ]
        ];
    }

    /**
     * @param $namespace
     * @param $aggs
     * @return array
     */
    public function all($namespace,$aggs) //global aggregation ... but can't use global as a function name
    {
        return [
            $namespace => [
                'global' => [],
                'aggs' => $aggs
            ]
        ];
    }

    /**
     * @param $namespace
     * @param $field
     * @param $interval
     * @param null $minimum_doc_count
     * @param array $order
     * @param int $offset
     * @param null $keyed
     * @param null $missing
     * @return array
     */
    public function histogram($namespace,$field,$interval,$order=['_key'=>'asc'],$offset=0,$keyed=null,$missing=null)
    {

        return [
            $namespace => [
                'histogram' => array_filter([
                    'field' => $field,
                    'interval' => $interval,
                    'order' => $order,
                    'offset' => $offset,
                    'keyed' => $keyed,
                    'missing' => $missing
                ])
            ]
        ];
    }

    /**
     * @param $namespace
     * @param $field
     * @return array
     */
    public function missing($namespace,$field)
    {
        return [
            $namespace => [
                'missing' => [
                    'field' => $field
                ]
            ]
        ];
    }

    /**
     * @param $namespace
     * @param $path
     * @param $aggs
     * @return array
     */
    public function nested($namespace,$path,$aggs)
    {
        return [
            $namespace => [
                'nested' => [
                    'path' => $path,
                    'aggs' => $aggs
                ]
            ]
        ];
    }

    /**
     * @param $namespace
     * @param $field
     * @return array
     */
    public function significant_terms($namespace,$field)
    {
        return [
            $namespace => [
                'significant_terms' => [
                    'field' => $field
                ]
            ]
        ];
    }
}