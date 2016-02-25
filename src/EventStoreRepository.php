<?php

namespace PhpInPractice\Matters\Aggregate;

use EventStore\EventStoreInterface;
use EventStore\StreamFeed\EntryWithEvent;
use EventStore\WritableEvent;

final class EventStoreRepository implements Repository
{
    /** @var EventStoreInterface */
    private $eventstore;

    /** @var string */
    private $aggregateClassName;

    /** @var StreamNameGenerator */
    private $streamNameGenerator;

    /** @var EventSerializer */
    private $eventSerializer;

    /** @var Transaction */
    private $transaction;

    /** @var null|MetadataStore */
    private $metadataStore;

    /**
     * EventStoreRepository constructor.
     *
     * @param StreamNameGenerator $streamNameGenerator
     * @param EventSerializer $eventSerializer
     * @param EventStoreInterface $eventstore
     * @param Transaction $transaction
     * @param string $aggregateClassName
     * @param MetadataStore|null $metadataStore
     */
    public function __construct(
        StreamNameGenerator $streamNameGenerator,
        EventSerializer $eventSerializer,
        EventStoreInterface $eventstore,
        Transaction $transaction,
        $aggregateClassName,
        MetadataStore $metadataStore = null
    ) {
        $this->eventstore          = $eventstore;
        $this->aggregateClassName  = $aggregateClassName;
        $this->streamNameGenerator = $streamNameGenerator;
        $this->eventSerializer     = $eventSerializer;
        $this->transaction         = $transaction;
        $this->metadataStore       = $metadataStore;
    }

    /**
     * @param IsEventSourced|object $aggregateRoot
     */
    public function persist($aggregateRoot)
    {
        $this->transaction->push(
            $this->streamNameGenerator->generate(get_class($aggregateRoot), (string)$aggregateRoot->id()),
            $this->getWriteableEventsFromAggregate($aggregateRoot)
        );
    }

    public function findById($uuid)
    {
        return $this->reconstituteAggregateFromDomainEvents(
            $this->aggregateClassName,
            $this->getDomainEventsFromStream(
                $this->streamNameGenerator->generate($this->aggregateClassName, (string)$uuid)
            )
        );
    }

    /**
     * @param $streamUri
     *
     * @return array
     */
    private function getDomainEventsFromStream($streamUri)
    {
        $events   = [];
        $iterator = $this->eventstore->forwardStreamFeedIterator($streamUri);
        /** @var EntryWithEvent $entry */
        foreach ($iterator as $entry) {
            $event    = $entry->getEvent();
            $events[] = $this->eventSerializer->unserialize($event->getType(), $event->getData());
        }

        return $events;
    }

    /**
     * @param $aggregateClassName
     * @param $events
     *
     * @return mixed|null
     */
    private function reconstituteAggregateFromDomainEvents($aggregateClassName, $events)
    {
        if ($events === []) {
            return null;
        }

        return call_user_func([$aggregateClassName, 'reconstituteFromHistory'], $events);
    }

    /**
     * @param object|IsEventSourced $aggregateRoot
     *
     * @return array
     */
    private function getWriteableEventsFromAggregate($aggregateRoot)
    {
        $domainEvents = $aggregateRoot->extractRecordedEvents();

        $eventsArray = [];
        foreach ($domainEvents as $event) {
            $metadata = [];
            if ($this->metadataStore) {
                $metadata = $this->metadataStore->metadata($event);
            }

            $eventsArray[] = WritableEvent::newInstance(
                get_class($event),
                $this->eventSerializer->serialize($event),
                $metadata
            );
        }

        return $eventsArray;
    }
}
