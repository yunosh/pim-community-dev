<?php

declare(strict_types=1);

namespace Pim\Bundle\CatalogBundle\tests\integration\Command;

use Akeneo\Bundle\ElasticsearchBundle\tests\integration\AbstractIndexCommandIntegration;
use Akeneo\Test\Integration\Configuration;
use Akeneo\Test\IntegrationTestsBundle\Launcher\CommandLauncher;

/**
 * Checks that the index product models command indexes all product models and their descendants in the right indexes.
 *
 * @author    Samir Boulil <samir.boulil@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class IndexProductModelsCommandIntegration extends AbstractIndexCommandIntegration
{
    public function testIndexesAllProductModelsAndTheirDescendants(): void
    {
        $this->assertIndexesEmpty();
        $this->runIndexProductModelsCommand();
        $this->assertIndexesNotEmpty(['akeneo_pim_product_model', 'akeneo_pim_product_and_product_model']);
    }

    public function testIndexesProductModelsAndTheirDescendantsWithIdentifiers(): void
    {
        $this->assertIndexesEmpty();
        $this->runIndexProductModelsCommand(['model-braided-hat', 'model-tshirt-divided']);
        $this->assertIndexesNotEmpty(['akeneo_pim_product_model', 'akeneo_pim_product_and_product_model']);
        $this->assertIndexesCount(5, ['akeneo_pim_product_model']);
        $this->assertIndexesCount(19, ['akeneo_pim_product_and_product_model']);
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
    private function runIndexProductModelsCommand(array $productModelIdentifiers = []): void
    {
        $options = $this->getIndexCommandOptions($productModelIdentifiers);
        $commandLauncher = new CommandLauncher($this->testKernel);
        $exitCode = $commandLauncher->execute('pim:product-model:index', null, $options);
        $this->assertSame(0, $exitCode);
    }

    /**
     * @param array $productModelCodes
     *
     * @return array
     */
    private function getIndexCommandOptions(array $productModelCodes): array
    {
        $options = ['arguments' => ['--all' => true]];
        if (!empty($productModelCodes)) {
            $options = ['arguments' => ['codes' => $productModelCodes]];
        }

        return $options;
    }
}
