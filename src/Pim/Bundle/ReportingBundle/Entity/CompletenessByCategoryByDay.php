<?php

declare(strict_types=1);

namespace Pim\Bundle\ReportingBundle\Entity;

class CompletenessByCategoryByDay
{
    /** @var int */
    private $id;
    /** @var \DateTime */
    private $day;
    /** @var string */
    private $categoryCode;
    /** @var string */
    private $channelCode;
    /** @var string */
    private $localeCode;
    /** @var int */
    private $completedProducts;
    /** @var int */
    private $totalProducts;

    public function __construct(
        \DateTime $day,
        string $categoryCode,
        string $channelCode,
        string $localeCode,
        int $completedProducts,
        int $totalProducts
    ) {
        $this->day = $day;
        $this->categoryCode = $categoryCode;
        $this->channelCode = $channelCode;
        $this->localeCode = $localeCode;
        $this->completedProducts = $completedProducts;
        $this->totalProducts = $totalProducts;
    }
}
