<?php

namespace PhpInPractice\Matters\Aggregate;

use EventStore\EventStoreInterface;
use PhpInPractice\Matters\Aggregate\EventSerializer\FromArrayToArray;
use PhpInPractice\Matters\Aggregate\StreamNameGenerator\SluggifiedNameAndId;
use PhpInPractice\Matters\StreamNameGenerator;

final class RepositoryFactory
{
    /** @var StreamNameGenerator */
    private $streamNameGenerator;

    /** @var EventSerializer */
    private $eventSerializer;

    /** @var EventStoreInterface */
    private $eventStore;

    public function __construct(
        EventStoreInterface $eventStore,
        StreamNameGenerator $streamNameGenerator = null,
        EventSerializer $eventSerializer = null
    ) {
        $this->streamNameGenerator = $streamNameGenerator ?: new SluggifiedNameAndId();
        $this->eventSerializer = $eventSerializer ?: new FromArrayToArray();
        $this->eventStore = $eventStore;
    }

    public function create($aggregateType)
    {
        return new Repository(
            $this->streamNameGenerator,
            $this->eventSerializer,
            $this->eventStore,
            $aggregateType
        );
    }
}
