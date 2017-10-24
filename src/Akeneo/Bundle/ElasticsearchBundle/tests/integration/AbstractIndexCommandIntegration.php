<?php

declare(strict_types=1);

namespace Akeneo\Bundle\ElasticsearchBundle\tests\integration;

use Akeneo\Test\Integration\TestCase;
use Akeneo\Test\IntegrationTestsBundle\Launcher\CommandLauncher;

/**
 * @author    Samir Boulil <samir.boulil@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class AbstractIndexCommandIntegration extends TestCase
{
    /**
     * Resets all ES indexes registered in the PIM.
     */
    protected function runResetIndexesCommand(): void
    {
        $commandLauncher = new CommandLauncher($this->testKernel);
        $exitCode = $commandLauncher->execute('akeneo:elasticsearch:reset-indexes', null, ['inputs' => ['yes']]);
        $this->assertSame(0, $exitCode);
    }

    /**
     * Checks wether the given indexes name are not empty, if none given will check that all registered indexes in the
     * PIM are not empty.
     *
     * @param array $indexesName
     */
    protected function assertIndexesNotEmpty(array $indexesName = []): void
    {
        $esClients = $this->get('akeneo_elasticsearch.registry.clients')->getClients();

        foreach ($esClients as $esClient) {
            if (!in_array($esClient->getIndexName(), $indexesName) && !empty($indexesName)) {
                continue;
            }

            $allDocuments = $esClient->search('pim_catalog_product', [
                '_source' => 'identifier',
                'query'   => [
                    'match_all' => new \StdClass(),
                ],
            ]);
            $this->assertGreaterThan(0, count($allDocuments['hits']['hits']));
        }
    }

    /**
     * Checks wether the given indexes name are empty, if none given it will check that all registered indexes in the
     * PIM are empty.
     *
     * @param array $indexesName
     */
    protected function assertIndexesEmpty(array $indexesName = []): void
    {
        $esClients = $this->get('akeneo_elasticsearch.registry.clients')->getClients();

        foreach ($esClients as $esClient) {
            if (!in_array($esClient->getIndexName(), $indexesName) && !empty($indexesName)) {
                continue;
            }

            $allDocuments = $esClient->search('pim_catalog_product', [
                'query' => [
                    'match_all' => new \StdClass(),
                ],
            ]);
            $this->assertEquals(0, count($allDocuments['hits']['hits']));
        }
    }

    /**
     * Checks wether the given indexes name are not empty, if none given will check that all registered indexes in the
     * PIM are not empty.
     *
     * @param int   $count
     * @param array $indexesName
     */
    protected function assertIndexesCount(int $count, array $indexesName = []): void
    {
        $esClients = $this->get('akeneo_elasticsearch.registry.clients')->getClients();

        foreach ($esClients as $esClient) {
            if (!in_array($esClient->getIndexName(), $indexesName) && !empty($indexesName)) {
                continue;
            }

            $allDocuments = $esClient->search('pim_catalog_product', [
                '_source' => 'identifier',
                'query'   => [
                    'match_all' => new \StdClass(),
                ],
            ]);
            $this->assertEquals($count, $allDocuments['hits']['total']);
        }
    }
}
