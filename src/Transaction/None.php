<?php

namespace PhpInPractice\Matters\Aggregate\Transaction;

use EventStore\EventStoreInterface;
use EventStore\WritableEventCollection;
use PhpInPractice\Matters\Aggregate\Transaction as TransactionInterface;

final class None implements TransactionInterface
{
    /**
     * @var EventStoreInterface
     */
    private $eventstore;

    public function __construct(EventStoreInterface $eventstore)
    {
        $this->eventstore = $eventstore;
    }

    public function push($streamUri, array $writeableEvents)
    {
        $collection = new WritableEventCollection($writeableEvents);
        $this->eventstore->writeToStream($streamUri, $collection);
    }

    public function commit()
    {
        // Doesn't need to do anything
        return null;
    }
}
