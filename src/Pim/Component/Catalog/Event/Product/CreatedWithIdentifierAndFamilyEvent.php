<?php

namespace Pim\Component\Catalog\Event\Product;

use Pim\Component\Catalog\Model\FamilyInterface;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Model\ValueInterface;
use Symfony\Component\EventDispatcher\Event;

class CreatedWithIdentifierAndFamilyEvent extends Event
{
    /** @var ProductInterface */
    private $product;

    /** @var ValueInterface */
    private $identifier;

    /** @var FamilyInterface */
    private $family;

    /**
     * @param ProductInterface $product
     * @param ValueInterface $identifier
     */
    public function __construct(ProductInterface $product, ValueInterface $identifier, FamilyInterface $family)
    {
        $this->product = $product;
        $this->identifier = $identifier;
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
     * @return ValueInterface
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return FamilyInterface
     */
    public function getFamily()
    {
        return $this->family;
    }
}
