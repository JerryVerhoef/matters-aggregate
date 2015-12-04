<?php

namespace PhpInPractice\Matters\Aggregate\Transaction;

use EventStore\EventStoreInterface;
use EventStore\WritableEvent;
use EventStore\WritableEventCollection;
use PhpInPractice\Matters\Aggregate\Transaction as TransactionInterface;

class SingleStream implements TransactionInterface
{
    /**
     * @var EventStoreInterface
     */
    private $eventstore;

    /** @var string */
    private $streamUri = '';

    /** @var WritableEvent[] */
    private $events = [];

    public function __construct(EventStoreInterface $eventstore)
    {
        $this->eventstore = $eventstore;
    }

    public function push($streamUri, array $writeableEvents)
    {
        if ($this->streamUri && $streamUri !== $this->streamUri) {
            throw new TransactionLimitedToASingleStreamException();
        }

        $this->streamUri = $streamUri;
        $this->events = array_merge($this->events, $writeableEvents);
    }

    public function flush()
    {
        $collection = new WritableEventCollection($this->events);
        $this->eventstore->writeToStream($this->streamUri, $collection);
    }
}
