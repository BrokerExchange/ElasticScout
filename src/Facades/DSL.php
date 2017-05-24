<?php
/**
 * Created by PhpStorm.
 * User: bmix
 * Date: 5/24/17
 * Time: 2:53 PM
 */

namespace ElasticScout\Facades;

use Illuminate\Support\Facades\Facade;

class DSL extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ElasticScout\Generators\DSL';
    }
}