<?php

namespace Pim\Component\Catalog\Event\Product;

use Pim\Component\Catalog\Event\DomainEvent;
use Pim\Component\Catalog\Model\AssociationTypeInterface;
use Pim\Component\Catalog\Model\GroupInterface;
use Pim\Component\Catalog\Model\ProductInterface;

/**
 * The product has been associated to a product group
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AssociatedToAGroupEvent extends DomainEvent
{
    /** @var ProductInterface */
    private $product;

    /** @var GroupInterface */
    private $associatedGroup;

    /** @var AssociationTypeInterface */
    private $associationType;

    /**
     * @param ProductInterface         $product
     * @param GroupInterface           $associatedGroup
     * @param AssociationTypeInterface $type
     */
    public function __construct(
        ProductInterface $product,
        GroupInterface $associatedGroup,
        AssociationTypeInterface $type)
    {
        $this->product = $product;
        $this->associatedGroup = $associatedGroup;
        $this->associationType = $type;
    }

    /**
     * @return ProductInterface
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return AssociationTypeInterface
     */
    public function getAssociationType()
    {
        return $this->associationType;
    }

    /**
     * @return GroupInterface
     */
    public function getAssociatedGroup()
    {
        return $this->associatedGroup;
    }
}
