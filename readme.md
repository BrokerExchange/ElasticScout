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
 * run composer require command
 `composer require brokerexchange\elasticscout`
 * Add Provider to config/app.php
 ```php
    ElasticScout\ElasticScoutServiceProvider::class,
 ```
 * Set default scout driver to "elastic" in .env file
 ```env
    SCOUT_DRIVER=elastic
 ```
 * Add trait to desired model
 ```php
    use ElasticScout\Searchable;
 ```
 * Add Facades to Alias list in config/app.php
 ```php
         'DSL'          => ElasticScout\Facades\Dsl::class,
         'Agg'          => Elasticscout\Facades\Agg::class,
 ```
 
## Usage
 ```php
    //create search/query object
    $search = $article->search()
        ->boolean()
        ->should(DSL::match('title',$request->input('query')))
        ->should(DSL::match('body',$request->input('query')))
        ->filter(DSL::term('published', 1))
        ->aggregate(Agg::terms('categories', 'category.name'));
    
    //retrieve aggregation results
    $categories = $search->aggregations('categories');
    
    //fire the search
    $results = $search->paginate();
 ```
## Mappings
 You can set a custom mapping by simply defining a "mapping" method on your model.
 
 ```php
    public function mappings()
    {
        return [
            $this->searchableType() => [
                'properties' => [
                    'name' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                            ],
                            'autocomplete' => [
                                'type' => 'text',
                                'analyzer' => 'autocomplete',
                                'search_analyzer' => 'autocomplete_search',
                            ],
                        ],
                    ],
                ]
            ]
        ]
    }
 ```
 
 ## Settings
  You can create custom settings and alalyzers by creating a "settings" method on the model.
  
 ```php
    public function settings()
    {
      return [
          'index' => [
              'analysis' => [
                  'analyzer' => [
                      'autocomplete' => [
                          'tokenizer' => 'autocomplete',
                          'filter' => [
                              'lowercase',
                          ],
                      ],
                      'autocomplete_search' => [
                          'tokenizer' => 'lowercase',
                      ],
                  ],
                  'tokenizer' => [
                      'autocomplete' => [
                          'type' => 'edge_ngram',
                          'min_gram' => 1,
                          'max_gram' => 15,
                          'token_chars' => [
                              'letter'
                          ]
                      ]
                  ]
              ],
          ],
      ];
    }
 ```