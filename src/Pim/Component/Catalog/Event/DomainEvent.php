<?php

namespace Pim\Component\Catalog\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Abstract DomainEvent to decouple our PIM domain events and models from Symfony event dispatcher component
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class DomainEvent extends Event
{
}
