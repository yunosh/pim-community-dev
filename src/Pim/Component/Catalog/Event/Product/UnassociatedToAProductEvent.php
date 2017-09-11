<?php

namespace Pim\Component\Catalog\Event\Product;

use Pim\Component\Catalog\Event\DomainEvent;
use Pim\Component\Catalog\Model\AssociationTypeInterface;
use Pim\Component\Catalog\Model\ProductInterface;

/**
 * The product has been unassociated to another product
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class UnassociatedToAProductEvent extends DomainEvent
{
    /** @var ProductInterface */
    private $product;

    /** @var ProductInterface */
    private $associatedProduct;

    /** @var AssociationTypeInterface */
    private $associationType;

    /**
     * @param ProductInterface         $product
     * @param ProductInterface         $associatedProduct
     * @param AssociationTypeInterface $type
     */
    public function __construct(
        ProductInterface $product,
        ProductInterface $associatedProduct,
        AssociationTypeInterface $type)
    {
        $this->product = $product;
        $this->associatedProduct = $associatedProduct;
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
     * @return ProductInterface
     */
    public function getAssociatedProduct()
    {
        return $this->associatedProduct;
    }

    /**
     * @return AssociationTypeInterface
     */
    public function getAssociationType()
    {
        return $this->associationType;
    }
}
