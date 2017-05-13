[![Latest Stable Version](https://poser.pugx.org/brokerexchange/elasticscout/v/stable)](https://packagist.org/packages/brokerexchange/elasticscout)
[![Latest Unstable Version](https://poser.pugx.org/brokerexchange/elasticscout/v/unstable)](https://packagist.org/packages/brokerexchange/elasticscout)
[![Total Downloads](https://poser.pugx.org/brokerexchange/elasticscout/downloads)](https://packagist.org/packages/brokerexchange/elasticscout)
[![License](https://poser.pugx.org/brokerexchange/elasticscout/license)](https://packagist.org/packages/brokerexchange/elasticscout)
[![composer.lock](https://poser.pugx.org/brokerexchange/elasticscout/composerlock)](https://packagist.org/packages/brokerexchange/elasticscout)

# ElasticScout
A [Laravel Scout](https://github.com/laravel/scout) Driver for Elasticsearch 5.x

## Overview
ElasticScout is a [Laravel Scout](https://github.com/laravel/scout) Elasticsearch 5.x compatible engine. It makes critical changes to the old Elasticseach Scout Engine, as well as adds additional functionality.

The ElasticScout engine includes an Elasticsearch Query Builder which can be used to create elaborate custom queries and aggregations, allowing full use of Elasticsearch within the Laravel/Scout Paradigm.

## License
ElasticScout is released under the MIT Open Source License, <https://opensource.org/licenses/MIT>

## Copyright
ElasticScout &copy; Broker Exchange Network

## Installation
 * run command `composer require brokerexchange\elasticscout`
 * Add `ElasticScout\ElasticScoutServiceProvider::class,` to config/app.php
 * Set default scout driver to "elastic" in .env file:  `SCOUT_DRIVER=elastic`
 * Add `use ElasticScout\Searchable;` trait to desired model

## Usage
`
$results = $article->search()
    ->boolean()
    ->should($article->match('title',$request->input('query')))
    ->should($article->match('body',$request->input('query')))
    ->filter($article->term('published', 1))
    ->aggregate($article->agg()->terms('categories', 'category.name'))
    ->paginate();
 `