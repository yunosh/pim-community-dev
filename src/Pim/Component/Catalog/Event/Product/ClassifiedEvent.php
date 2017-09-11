<?php

namespace Pim\Component\Catalog\Event\Product;

use Pim\Component\Catalog\Event\DomainEvent;
use Pim\Component\Catalog\Model\CategoryInterface;
use Pim\Component\Catalog\Model\ProductInterface;

/**
 * The product has been classified in a category
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ClassifiedEvent extends DomainEvent
{
    /** @var ProductInterface */
    private $product;

    /** @var CategoryInterface */
    private $category;

    /**
     * @param ProductInterface $product
     * @param CategoryInterface $category
     */
    public function __construct(ProductInterface $product, CategoryInterface $category)
    {
        $this->product = $product;
        $this->category = $category;
    }

    /**
     * @return ProductInterface
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return CategoryInterface
     */
    public function getCategory()
    {
        return $this->category;
    }
}
