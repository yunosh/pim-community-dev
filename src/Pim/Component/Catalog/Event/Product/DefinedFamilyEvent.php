<?php

namespace Pim\Component\Catalog\Event\Product;

use Pim\Component\Catalog\Model\FamilyInterface;
use Pim\Component\Catalog\Model\ProductInterface;
use Symfony\Component\EventDispatcher\Event;

class DefinedFamilyEvent extends Event
{
    /** @var ProductInterface */
    private $product;

    /** @var FamilyInterface */
    private $family;

    public function __construct(ProductInterface $product, FamilyInterface $family)
    {
        $this->product = $product;
        $this->family = $family;
    }

    /**
     * @return ProductInterface
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return FamilyInterface
     */
    public function getFamily()
    {
        return $this->family;
    }
}
