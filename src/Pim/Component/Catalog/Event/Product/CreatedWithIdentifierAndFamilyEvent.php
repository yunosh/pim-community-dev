<?php

namespace Pim\Component\Catalog\Event\Product;

use Pim\Component\Catalog\Event\DomainEvent;
use Pim\Component\Catalog\Model\FamilyInterface;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Model\ValueInterface;

/**
 * The product has been created with an identifier and a family
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CreatedWithIdentifierAndFamilyEvent extends DomainEvent
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
