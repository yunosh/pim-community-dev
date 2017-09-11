<?php

namespace Pim\Component\Catalog\Event\Product;

use Pim\Component\Catalog\Event\DomainEvent;
use Pim\Component\Catalog\Model\ProductInterface;

/**
 * The product has been disabled
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DisabledEvent extends DomainEvent
{
    /** @var ProductInterface */
    private $product;

    /**
     * @param ProductInterface $product
     */
    public function __construct(ProductInterface $product)
    {
        $this->product = $product;
    }

    /**
     * @return ProductInterface
     */
    public function getProduct()
    {
        return $this->product;
    }
}
