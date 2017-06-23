<?php

namespace Pim\Component\Catalog\Event\Product;

use Pim\Component\Catalog\Model\ProductInterface;
use Symfony\Component\EventDispatcher\Event;

class EnabledEvent extends Event
{
    /** @var ProductInterface */
    private $product;

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
