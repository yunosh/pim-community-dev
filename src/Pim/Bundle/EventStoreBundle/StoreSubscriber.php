<?php

namespace Pim\Bundle\EventStoreBundle;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Pim\Bundle\UserBundle\Context\UserContext;
use Pim\Component\Catalog\Event\Product\ChangedFamilyEvent;
use Pim\Component\Catalog\Event\Product\ClassifiedEvent;
use Pim\Component\Catalog\Event\Product\CompletedForChannelAndLocale;
use Pim\Component\Catalog\Event\Product\CreatedWithIdentifierAndFamilyEvent;
use Pim\Component\Catalog\Event\Product\CreatedWithIdentifierEvent;
use Pim\Component\Catalog\Event\Product\CreatedWithoutIdentifierEvent;
use Pim\Component\Catalog\Event\Product\DefinedFamilyEvent;
use Pim\Component\Catalog\Event\Product\DisabledEvent;
use Pim\Component\Catalog\Event\Product\EnabledEvent;
use Pim\Component\Catalog\Event\Product\FulfilledExistingValueEvent;
use Pim\Component\Catalog\Event\Product\FulfilledNewValueEvent;
use Pim\Component\Catalog\Event\Product\UnclassifiedEvent;
use Pim\Component\Catalog\Event\Product\UncompletedForChannelAndLocale;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Listen, enrich and store domain events
 *
 * This is not done directly in the product saver as it's only a technical
 * problem. The product saver only handles business stuff.
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class StoreSubscriber implements EventSubscriberInterface
{
    /** @var LoggerInterface */
    private $logger;

    /** @var UserContext */
    private $userContext;

    public function __construct(LoggerInterface $logger, UserContext $userContext)
    {
        $this->logger = $logger;
        $this->userContext = $userContext;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            CreatedWithIdentifierAndFamilyEvent::class => 'store',
            CreatedWithIdentifierEvent::class => 'store',
            CreatedWithoutIdentifierEvent::class => 'store',
            FulfilledExistingValueEvent::class => 'store',
            FulfilledNewValueEvent::class => 'store',
            ClassifiedEvent::class => 'store',
            UnclassifiedEvent::class => 'store',
            DefinedFamilyEvent::class => 'store',
            ChangedFamilyEvent::class => 'store',
            CompletedForChannelAndLocale::class => 'store',
            UncompletedForChannelAndLocale::class => 'store',
            EnabledEvent::class => 'store',
            DisabledEvent::class => 'store',
        ];
    }

    /**
     * @param Event $event
     */
    public function store(Event $event)
    {
        // TODO: to be extracted in event enricher

        $message = [
            'type' => get_class($event),
            'aggregate' => 'product',
            'aggregate_id' => $event->getProduct()->getId(),
            'data' => [],
            'metadata' => ['username' => $this->getUsername()]
        ];
        if (method_exists($event, 'getLocale')) {
            $message['data']['locale']= $event->getLocale()->getCode();
        }
        if (method_exists($event, 'getChannel')) {
            $message['data']['channel']= $event->getChannel()->getCode();
        }
        if (method_exists($event, 'getValue')) {
            $valueData = $event->getValue()->getData();
            if ($valueData instanceof Collection) {
                $normalizedData = [];
                foreach ($valueData as $item) {
                    $normalizedData[] = (string) $item;
                }
            } else {
                $normalizedData = (string) $valueData;
            }
            $message['data']['value']= $normalizedData;
        }
        if (method_exists($event, 'getFamily')) {
            $message['data']['family']= (string)$event->getFamily()->getCode();
        }
        if (method_exists($event, 'getCategory')) {
            $message['data']['category']= (string)$event->getCategory()->getCode();
        }
        if (method_exists($event, 'getIdentifier')) {
            $message['data']['value']= (string)$event->getIdentifier()->getData();
        }

        $this->logger->info(json_encode($message), []);
    }

    /**
     * @return string
     */
    private function getUsername()
    {
        return $this->userContext->getUser()->getUsername();
    }
}
