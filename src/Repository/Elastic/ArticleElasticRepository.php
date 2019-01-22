<?php

namespace App\Repository\Elastic;

use App\Form\Filter\ArticleFilter;

class ArticleElasticRepository extends ElasticRepository
{
    public function getType()
    {
        return $this->index->getType('article');
    }

    public function getIds(array $filter)
    {
        if ($query = $filter['query'] ?? null) {
            switch ($filter['queryfor']) {
                case ArticleFilter::QUERYFOR_TITLE:
                    $fields = ['title'];
                    break;
                case ArticleFilter::QUERYFOR_CONTENT:
                    $fields = ['content'];
                    break;
                default:
                    $fields = ['title', 'content'];
                    break;
            }

            $params = [
                '_source' => false,
                'query' => [
                    'simple_query_string' => [
                        'query' => $query,
                        'fields' => $fields
                    ]
                ]
            ];
            $results = $this->getType()->search($params)->getResults();

            $articleIds = [];
            foreach ($results as $result) {
                $articleIds[] = $result->getId();
            }

            return $articleIds ?: [0];
        }

        return [];
    }
}