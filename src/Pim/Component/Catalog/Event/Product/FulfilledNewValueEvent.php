<?php

namespace Pim\Component\Catalog\Event\Product;

use Pim\Component\Catalog\Event\DomainEvent;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Model\ValueInterface;

/**
 * A new product value has fulfilled
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class FulfilledNewValueEvent extends DomainEvent
{
    /** @var ProductInterface */
    private $product;

    /** @var ValueInterface */
    private $value;

    /**
     * @param ProductInterface $product
     * @param ValueInterface $value
     */
    public function __construct(ProductInterface $product, ValueInterface $value)
    {
        $this->product = $product;
        $this->value = $value;
    }

    /**
     * @return ProductInterface
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return ValueInterface
     */
    public function getValue()
    {
        return $this->value;
    }
}
