<?php
/**
 * Created by PhpStorm.
 * User: bmix
 * Date: 5/12/17
 * Time: 1:34 PM
 */

namespace ElasticScout\Generators;

/**
 * Class DSL
 * @package ElasticScout
 */
class DSL
{

    /**
     * @param $field
     * @param $value
     * @return array
     */
    public function term($field,$value)
    {
        if(!empty($value) && !empty($field)) {
            return [
                'term' => [
                    $field => $value
                ]
            ];
        }

        return [];
    }

    /**
     * @param $field
     * @param array $values
     * @return array
     */
    public function terms($field,$values=[])
    {
        if(!empty($field) && count($values)) {
            return [
                'terms' => [
                    $field => $values
                ]
            ];
        }

        return [];

    }

    /**
     * @param $field
     * @param array $ranges
     * @param int $boost
     * @return array
     */
    public function range($field,$ranges=[],$boost=null)
    {

        if(count($ranges) && !empty($field)) {
            if(!is_null($boost))
            {
                $ranges = array_merge($ranges,['boost'=>$boost]);
            }

            return [
                'range' => [
                    $field => $ranges
                ]
            ];
        }

        return [];

    }

    /**
     * @param $field
     * @param $query
     * @param string $operator
     * @param string $type
     * @param int $minimum
     * @param int $boost
     * @param string $analyzer
     * @param int $fuzziness
     * @return array
     */
    public function match($field,$query,$operator=null,$type=null,$minimum=null,$boost=null,$analyzer=null,$fuzziness=null)
    {

        $params = [];

        if(!empty($field) && !empty($query)) {
            $params = [
                'match' => [
                    $field => array_filter([
                        'query' => $query,
                        'operator' => $operator,
                        'type' => $type,
                        'analyzer' => $analyzer,
                        'minimum_should_match' => $minimum,
                        'boost' => $boost,
                        'fuzziness' => $fuzziness
                    ])
                ]
            ];
        }

        return $params;
    }

    /**
     * @param array $fields
     * @param $query
     * @param string $operator
     * @param string $type
     * @param int $minimum
     * @param int $boost
     * @param string $analyzer
     * @param int $fuzziness
     * @return array
     */
    public function multi_match($fields=[],$query,$operator=null,$type=null,$minimum=null,$boost=null,$analyzer=null,$fuzziness=null)
    {
        $params = [];

        if(count($fields) && !empty($query)) {
            $params = [
                'multi_match' => array_filter([
                    'query' => $query,
                    'type' => $type,
                    'fields' => $fields,
                    'operator' => $operator,
                    'analyzer' => $analyzer,
                    'minimum_should_match' => $minimum,
                    'fuzziness' => $fuzziness,
                    'boost' => $boost,
                ])
            ];
        }

        return $params;
    }

    /**
     * @return array
     */
    public function match_all()
    {
        return [
            'match_all' => new \stdClass
        ];
    }

    /**
     * @param $field
     * @param $value
     * @param int $boost
     * @param float $cuttoff
     * @param string $low_freq_operator
     * @param string $high_freq_operator
     * @param string $analyzer
     * @return array
     */
    public function common($field,$value,$boost=0,$cuttoff=0.0,$low_freq_operator=null,$high_freq_operator=null,$analyzer=null)
    {
        return [
            'common' => [
                $field => array_filter([
                    'query' => $value,
                    'cuttoff_frequency' => $cuttoff,
                    'low_freq_operator' => $low_freq_operator,
                    'high_freq_operator' => $high_freq_operator,
                    'boost' => $boost,
                    'analyzer' => $analyzer,
                ])
            ]
        ];
    }

    /**
     * @param $field
     * @return array
     */
    public function exists($field)
    {
        return [
            'exits' => [
                'field' => $field
            ]
        ];
    }

    /**
     * @param $field
     * @return array
     */
    public function missing($field)
    {
        return [
            'missing' => [
                'field' => $field
            ]
        ];
    }

    /**
     * @param $field
     * @param $value
     * @param int $boost
     * @return array
     */
    public function prefix($field,$value,$boost=0)
    {
        return [
            'prefix' => [
                $field => array_filter([
                    'value' => $value,
                    'boost' => $boost
                ])
            ]
        ];
    }

    /**
     * @param $field
     * @param $value
     * @param int $boost
     * @param string $fuzziness
     * @param int $prefix_length
     * @param int $max_exp
     * @return array
     */
    public function fuzzy($field,$value,$boost=0,$fuzziness=null,$prefix_length=0,$max_exp=0)
    {
        return [
            'fuzzy' => [
                $field => array_filter([
                    'value' => $value,
                    'boost' => $boost,
                    'fuzziness' => $fuzziness,
                    'prefix_length' => $prefix_length,
                    'max_expansions' => $max_exp
                ])
            ]
        ];
    }

    /**
     * @param $type
     * @param array $values
     * @return array
     */
    public function ids($type,$values=[])
    {
        return [
            'ids' => [
                'type' => $type,
                'values' => $values
            ]
        ];
    }

    /**
     * @param $value
     * @return array
     */
    public function limit($value)
    {
        return [
            'limit' => [
                'value' => $value
            ]
        ];
    }

    /**
     * @param $path
     * @param $query
     * @param string $score_mode
     * @return array
     */
    public function nested($path,$query,$score_mode=null)
    {
        return [
            'nested' => array_filter([
                'path' => $path,
                'query' => $query,
                'score_mode' => $score_mode
            ])
        ];
    }

    /**
     * @param $type
     * @param $query
     * @param string $score_mode
     * @param $min_children
     * @param $max_children
     * @return array
     */
    public function has_child($type,$query,$score_mode=null,$min_children,$max_children)
    {
        $query = [
            'has_child' => array_filter([
                'type' => $type,
                'query' => $query,
                'score_mode' => $score_mode
            ])
        ];

        if(!empty($min_children) && is_numeric($min_children)){
            $query['has_child']['min_children'] = $min_children;
        }

        if(!empty($max_children) && is_numeric($max_children)){
            $query['has_child']['min_children'] = $max_children;
        }

        return $query;
    }

    /**
     * @param $type
     * @param $query
     * @param string $score_mode
     * @return array
     */
    public function has_parent($type,$query,$score_mode=null)
    {
        $query = [
            'has_child' => array_filter([
                'parent_type' => $type,
                'query' => $query,
                'score_mode' => $score_mode
            ])
        ];

        return $query;
    }


    /**
     * @param array $functions
     * @return array
     */
    public function function_score($functions=[])
    {
        return [
            'function_score' => [
                'functions' => $functions
            ]
        ];
    }

    /**
     * @param $field
     * @param $origin
     * @param $offset
     * @param $scale
     * @param int $weight
     * @return array
     */
    public function gauss($field,$origin,$offset,$scale,$weight=1)
    {

        $args = [
            'origin' => $origin,
            'scale' => $scale
        ];

        if(!empty($offset))
        {
            $args = array_merge($args,['offset'=>$offset]);
        }

        return [
            'gauss' => [
                $field => $args
            ],
            'weight' => $weight
        ];
    }

    /**
     * @param $field
     * @param $top_left
     * @param $bottom_right
     * @return array
     */
    public function geo_bounding_box($field,$top_left,$bottom_right)
    {
        return [
            'geo_bounding_box' => [
                $field => [
                    'top_left' => $top_left,
                    'bottom_right' => $bottom_right
                ]
            ]
        ];
    }

    /**
     * @param $field
     * @param $origin
     * @param $distance
     * @return array
     */
    public function geo_distance($field,$origin,$distance)
    {
        return [
            'geo_distance' => [
                'distance' => $distance,
                $field => $origin
            ]
        ];
    }

    /**
     * @param $query
     * @return array
     */
    public function not($query)
    {
        return [
            'not' => $query
        ];
    }

    /**
     * @param $value
     * @return array
     */
    public function type($value)
    {
        return [
            'type' => [
                'value' => $value
            ]
        ];
    }
}