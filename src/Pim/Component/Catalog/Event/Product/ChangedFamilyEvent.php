<?php

namespace Pim\Component\Catalog\Event\Product;

use Pim\Component\Catalog\Event\DomainEvent;
use Pim\Component\Catalog\Model\FamilyInterface;
use Pim\Component\Catalog\Model\ProductInterface;

/**
 * The product's family has been changed
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ChangedFamilyEvent extends DomainEvent
{
    /** @var ProductInterface */
    private $product;

    /** @var FamilyInterface */
    private $family;

    /**
     * @param ProductInterface $product
     * @param FamilyInterface $family
     */
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
