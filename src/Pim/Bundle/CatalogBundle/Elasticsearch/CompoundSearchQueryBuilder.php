<?php

namespace Pim\Bundle\CatalogBundle\Elasticsearch;

class CompoundSearchQueryBuilder implements SearchQueryBuilder
{
    /** @var SearchQueryBuilder[] */
    private $filterClauses = [];

    /** @var SearchQueryBuilder[] */
    private $mustNotClauses = [];

    /** @var SearchQueryBuilder[] */
    private $shouldClauses = [];

    public function addFilter(SearchQueryBuilder $sqb)
    {
        $this->filterClauses[] = $sqb;
    }

    public function addMustNot(SearchQueryBuilder $sqb)
    {
        $this->mustNotClauses[] = $sqb;
    }

    public function addShould(SearchQueryBuilder $sqb)
    {
        $this->shouldClauses[] = $sqb;
    }

    /**
     * Returns an Elastic search Query
     *
     * @param array $source
     *
     * @return array
     */
    public function getQuery(array $source = [])
    {
        $searchQuery = [];
        foreach ($this->filterClauses as $filterClause) {
            $searchQuery['bool']['filter'][] = $filterClause->getQuery();
        }

        foreach ($this->mustNotClauses as $mustNotClause) {
            $searchQuery['bool']['must_not'][] = $mustNotClause->getQuery();
        }

        foreach ($this->shouldClauses as $shouldClause) {
            $searchQuery['bool']['minimum_should_match'] = 1;
            $searchQuery['bool']['should'][] = $shouldClause->getQuery();
        }

        return $searchQuery;
    }
}
