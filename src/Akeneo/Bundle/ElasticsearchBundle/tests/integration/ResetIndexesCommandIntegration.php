<?php

declare(strict_types=1);

namespace Akeneo\Bundle\ElasticsearchBundle\tests\integration;

use Akeneo\Test\Integration\Configuration;

/**
 * Checks that the reset index command resets all indexes registered in the PIM.
 *
 * @author    Samir Boulil <samir.boulil@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ResetIndexesCommandIntegration extends AbstractIndexCommandIntegration
{
    public function testCommandResetsAllIndexes()
    {
        $this->assertIndexesNotEmpty();
        $this->runResetIndexesCommand();
        $this->assertIndexesEmpty();
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration(): Configuration
    {
        return $this->catalog->useFunctionalCatalog('catalog_modeling');
    }
}
