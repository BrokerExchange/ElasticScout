[![Latest Stable Version](https://poser.pugx.org/brokerexchange/elasticscout/v/stable)](https://packagist.org/packages/brokerexchange/elasticscout)
[![Latest Unstable Version](https://poser.pugx.org/brokerexchange/elasticscout/v/unstable)](https://packagist.org/packages/brokerexchange/elasticscout)
[![Total Downloads](https://poser.pugx.org/brokerexchange/elasticscout/downloads)](https://packagist.org/packages/brokerexchange/elasticscout)
[![License](https://poser.pugx.org/brokerexchange/elasticscout/license)](https://packagist.org/packages/brokerexchange/elasticscout)
[![composer.lock](https://poser.pugx.org/brokerexchange/elasticscout/composerlock)](https://packagist.org/packages/brokerexchange/elasticscout)

# ElasticScout
A [Laravel Scout](https://github.com/laravel/scout) Driver for Elasticsearch 6

## Overview
ElasticScout is a [Laravel Scout](https://github.com/laravel/scout) Elasticsearch 6 compatible engine. It makes critical changes to the old Elasticseach Scout Engine, as well as adds new functionality.

The ElasticScout engine includes an Elasticsearch Query Builder which can be used to create elaborate custom queries and aggregations, allowing full use of Elasticsearch within the Laravel/Scout Paradigm.

## License
ElasticScout is released under the MIT Open Source License, <https://opensource.org/licenses/MIT>

## Copyright
ElasticScout &copy; Broker Exchange Network

## Installation
 * Run composer require command
 `composer require brokerexchange/elasticscout`
 * Configure Elasticsearch Host (default: localhost:9200)
 ```env
    ELASTICSEARCH_HOST='elastic1.host.com:9200,elastic2.host.com:9200'
 ```
 * Add trait to desired model
 ```php
    use ElasticScout\Searchable;
 ```
 
## Usage
 ```php
    //create search/query object
    $search = $article->search()
        ->boolean()
        ->should(DSL::match('title',$request->input('query')))
        ->should(DSL::match('body',$request->input('query')))
        ->highlight(['body','title'])
        ->filter(DSL::term('published', 1))
        ->aggregate(Agg::terms('categories', 'category.name'));
        
    //fire the search
    $articles = $search->paginate();
        
    //retrieve aggregation results
    $categories = $search->aggregation('categories');
    
    //retrieve highlight results for title field of first result article
    $firstArticleTitleHighlights = $articles->first()->highlight('title');
    
 ```
## Mappings
 You may set a custom mapping by simply defining a "mapping" method on your model.
 
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
  You may create custom settings and analyzers by creating a "settings" method on the model.
  
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
