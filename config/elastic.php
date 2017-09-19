<?php
/**
 * Created by PhpStorm.
 * User: bmix
 * Date: 9/1/17
 * Time: 8:23 AM
 */

return [
    'hosts' => explode(',',env('ELASTICSEARCH_HOST','localhost:9200'))
];