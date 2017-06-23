<?php

namespace Pim\Component\Catalog\Event\Product;

use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Model\ValueInterface;
use Symfony\Component\EventDispatcher\Event;

class FulfilledNewValueEvent extends Event
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
