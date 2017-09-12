<?php

declare(strict_types=1);

namespace Pim\Bundle\EventStoreBundle\Entity;

class Event
{
    /** @var int */
    private $id;
    /** @var \DateTime */
    private $createdAt;
    /** @var string */
    private $aggregate;
    /** @var int */
    private $aggregateId;
    /** @var string */
    private $type;
    /** @var array */
    private $data;
    /** @var array */
    private $metadata;

    public function __construct(
        \DateTime $createdAt,
        string $aggregate,
        int $aggregateId,
        string $type,
        array $data,
        array $metadata
    ) {
        $this->createdAt = $createdAt;
        $this->aggregate = $aggregate;
        $this->aggregateId = $aggregateId;
        $this->type = $type;
        $this->data = $data;
        $this->metadata = $metadata;
    }
}
