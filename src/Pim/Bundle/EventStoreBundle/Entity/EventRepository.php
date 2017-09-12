<?php

namespace Pim\Bundle\EventStoreBundle\Entity;

use Doctrine\Common\Persistence\ObjectManager;

class EventRepository
{
    private $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function add(Event $event)
    {
        $this->objectManager->persist($event);
        $this->objectManager->flush();
    }
}
