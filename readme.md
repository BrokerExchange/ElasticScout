[![Latest Stable Version](https://poser.pugx.org/brokerexchange/elasticscout/v/stable)](https://packagist.org/packages/brokerexchange/elasticscout)
[![Latest Unstable Version](https://poser.pugx.org/brokerexchange/elasticscout/v/unstable)](https://packagist.org/packages/brokerexchange/elasticscout)
[![Total Downloads](https://poser.pugx.org/brokerexchange/elasticscout/downloads)](https://packagist.org/packages/brokerexchange/elasticscout)
[![License](https://poser.pugx.org/brokerexchange/elasticscout/license)](https://packagist.org/packages/brokerexchange/elasticscout)
[![composer.lock](https://poser.pugx.org/brokerexchange/elasticscout/composerlock)](https://packagist.org/packages/brokerexchange/elasticscout)

# ElasticScout
Laravel Scout Driver for Elasticsearch

## Overview
[Laravel Scout](https://github.com/laravel/scout) is great! But, the Elasticsearch engine could be better in regards to being more 2.0/5.0 friendly. It useses a depricated "filtered" 
query that is is not compatible with Elasticsearch 5.0. It also uses a single index for all models, potentially causing conflicts between fields with the same names, but different data types.

The ElasticScout engine uses a standard bool query with must and filter clauses to match all fields.
It also uses a unique index for each model, instead of unique types in a single index per model. This ensure you will not have field type conflicts.

## License
ElasticScout is released under the MIT Open Source License, <https://opensource.org/licenses/MIT>

## Copyright
ElasticScout &copy; Broker Exchange Network

## Installation
 * run command `composer require brokerexchange\elasticscout`
 * Add `ElasticQueue\ElasticScoutServiceProvider::class,` to config/app.php
 * Set default scout driver to "elastic" in .env file:  `SCOUT_DRIVER=elastic`
