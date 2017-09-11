<?php

namespace Pim\Component\Catalog\Event\Product;

use Pim\Component\Catalog\Event\DomainEvent;
use Pim\Component\Catalog\Model\ChannelInterface;
use Pim\Component\Catalog\Model\LocaleInterface;
use Pim\Component\Catalog\Model\ProductInterface;

/**
 * The product has been uncompleted on a channel - locale scope
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class UncompletedForChannelAndLocale extends DomainEvent
{
    /** @var ProductInterface */
    private $product;
    /** @var ChannelInterface */
    private $channel;
    /** @var LocaleInterface */
    private $locale;

    /**
     * @param ProductInterface $product
     * @param ChannelInterface $channel
     * @param LocaleInterface  $locale
     */
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
     * @return LocaleInterface
     */
    public function getLocale()
    {
        return $this->locale;
    }
}
