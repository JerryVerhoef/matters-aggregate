<?php

namespace PhpInPractice\Matters\Aggregate;

use EventStore\EventStoreInterface;
use PhpInPractice\Matters\Aggregate\EventSerializer\FromArrayToArray;
use PhpInPractice\Matters\Aggregate\StreamNameGenerator\SluggifiedNameAndId;
use PhpInPractice\Matters\Aggregate\Transaction\None;

final class EventStoreRepositoryFactory implements RepositoryFactory
{
    /** @var StreamNameGenerator */
    private $streamNameGenerator;

    /** @var EventSerializer */
    private $eventSerializer;

    /** @var EventStoreInterface */
    private $eventStore;

    /** @var Transaction */
    private $transaction;

    /** @var MetadataStore */
    private $metadataStore;

    public function __construct(
        EventStoreInterface $eventStore,
        StreamNameGenerator $streamNameGenerator = null,
        EventSerializer $eventSerializer = null,
        Transaction $transaction = null,
        MetadataStore $metadataStore = null
    ) {
        $this->streamNameGenerator = $streamNameGenerator ?: new SluggifiedNameAndId();
        $this->eventSerializer = $eventSerializer ?: new FromArrayToArray();
        $this->eventStore = $eventStore;
        $this->transaction = $transaction ?: new None($eventStore);
        $this->metadataStore = $metadataStore;
    }

    /**
     * @param string $aggregateType
     * @return Repository
     */
    public function create($aggregateType)
    {
        return new EventStoreRepository(
            $this->streamNameGenerator,
            $this->eventSerializer,
            $this->eventStore,
            $this->transaction,
            $aggregateType,
            $this->metadataStore
        );
    }
}
