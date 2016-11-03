# ElasticScout
Laravel Scout Driver for Elasticsearch 5.0+

## Overview
Laravel Scout is great! But, the Elasticsearch engine could be better in regards to being more 5.0 friendly. This engine uses a standard bool query with must and filter clauses to match all fields.
It also uses a unique index for each model, instead of unique types in a single index per model. This ensure you will not have field type conflicts.

## License
ElasticQueue is released under the MIT Open Source License, <https://opensource.org/licenses/MIT>

## Copyright
ElasticQueue &copy; Broker Exchange Network

## Installation
 * run command `composer require brokerexchange\elasticscout`
 * Add `ElasticQueue\ElasticScoutServiceProvider::class,` to config/app.php
 * Set default scout driver to "elastic" in .env file:  `SCOUT_DRIVER=elastic`
