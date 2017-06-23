<?php

namespace Pim\Component\Catalog\Event\Product;

use Pim\Component\Catalog\Model\CategoryInterface;
use Pim\Component\Catalog\Model\ProductInterface;
use Symfony\Component\EventDispatcher\Event;

class UnclassifiedEvent extends Event
{
    /** @var ProductInterface */
    private $product;

    /** @var CategoryInterface */
    private $category;

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
