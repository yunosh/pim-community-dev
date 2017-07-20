<?php

namespace Pim\Bundle\CatalogBundle\Elasticsearch;

/**
 * {description}
 *
 * @author    Samir Boulil <samir.boulil@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class EsEnveloppe implements SearchQueryBuilderSearchQueryBuilder
{
    /** @var SearchQueryBuilder */
    private $sqb;

    /**
     * @param SearchQueryBuilder $sqb
     */
    public function __construct(SearchQueryBuilder $sqb)
    {
        $this->sqb = $sqb;
    }

    /**
     * Returns a complete Elastic search Query
     *
     * @param array $source
     *
     * @return array
     */
    public function getQuery(array $source = [])
    {
        if (empty($source)) {
            $source = ['identifier'];
        }

        $searchQuery = [
            '_source' => $source,
            'query'   => new \stdClass(),
        ];

        $queries = $this->sqb->getQuery($source);

        if (0 !== count($queries)) {
            $searchQuery['query'] = $queries;
        }

        return $searchQuery;
    }
}
