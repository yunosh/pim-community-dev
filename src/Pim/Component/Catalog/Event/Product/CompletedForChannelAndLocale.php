<?php

namespace Pim\Component\Catalog\Event\Product;

use Pim\Component\Catalog\Model\ChannelInterface;
use Pim\Component\Catalog\Model\LocaleInterface;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Validator\Constraints\Locale;
use Symfony\Component\EventDispatcher\Event;

class CompletedForChannelAndLocale extends Event
{
    /** @var ProductInterface */
    private $product;
    /** @var ChannelInterface */
    private $channel;
    /** @var Locale */
    private $locale;

    public function __construct(ProductInterface $product, ChannelInterface $channel, LocaleInterface $locale)
    {
        $this->product = $product;
        $this->channel = $channel;
        $this->locale = $locale;
    }

    /**
     * @return ProductInterface
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return ChannelInterface
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return Locale
     */
    public function getLocale()
    {
        return $this->locale;
    }
}
