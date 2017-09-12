<?php

declare(strict_types=1);

namespace Pim\Bundle\ReportingBundle\Entity;

use Doctrine\Common\Persistence\ObjectManager;

class CompletenessByCategoryByDayRepository
{
    private $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function add(CompletenessByCategoryByDay $event)
    {
        $this->objectManager->persist($event);
        $this->objectManager->flush();
    }
}
