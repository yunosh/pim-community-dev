<?php

declare(strict_types=1);

namespace Pim\Bundle\CatalogBundle\tests\integration\Command;

use Akeneo\Bundle\ElasticsearchBundle\tests\integration\AbstractIndexCommandIntegration;
use Akeneo\Test\Integration\Configuration;
use Akeneo\Test\IntegrationTestsBundle\Launcher\CommandLauncher;

/**
 * Checks that the index product command indexes all products in the right indexes.
 *
 * @author    Samir Boulil <samir.boulil@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class IndexProductsCommandIntegration extends AbstractIndexCommandIntegration
{
    public function testIndexesAllProducts(): void
    {
        $this->assertIndexesEmpty();
        $this->runIndexProductsCommand();
        $this->assertIndexesNotEmpty(['akeneo_pim_product', 'akeneo_pim_product_and_product_model']);
    }

    public function testIndexesProductsWithIdentifiers(): void
    {
        $this->assertIndexesEmpty();
        $this->runIndexProductsCommand(['watch', '1111111319']);
        $this->assertIndexesNotEmpty(['akeneo_pim_product', 'akeneo_pim_product_and_product_model']);
        $this->assertIndexesCount(2, ['akeneo_pim_product', 'akeneo_pim_product_and_product_model']);
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration(): Configuration
    {
        return $this->catalog->useFunctionalCatalog('catalog_modeling');
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->runResetIndexesCommand();
    }

    /**
     * Runs the index product command.
     */
    private function runIndexProductsCommand(array $productIdentifiers = []): void
    {
        $options = $this->getIndexCommandOptions($productIdentifiers);
        $commandLauncher = new CommandLauncher(static::$kernel);
        $exitCode = $commandLauncher->execute('pim:product:index', null, $options);
        $this->assertSame(0, $exitCode);
    }

    /**
     * @param array $productIdentifiers
     *
     * @return array
     */
    private function getIndexCommandOptions(array $productIdentifiers): array
    {
        $options = ['arguments' => ['--all' => true]];
        if (!empty($productIdentifiers)) {
            $options = ['arguments' => ['identifiers' => $productIdentifiers]];
        }

        return $options;
    }
}
