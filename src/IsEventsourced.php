<?php

namespace PhpInPractice\Matters\Aggregate;

trait IsEventsourced
{
    /**
     * List of events that are not committed to the EventStore
     *
     * @var object[]
     */
    private $recordedEvents = array();

    abstract public function id();

    /**
     * @param object[] $historyEvents
     * @return static
     */
    public static function reconstituteFromHistory(array $historyEvents)
    {
        $instance = new static();
        $instance->replay($historyEvents);

        return $instance;
    }

    /**
     * Get pending events and reset stack
     *
     * @return object[]
     */
    public function extractRecordedEvents()
    {
        $pendingEvents = $this->recordedEvents;

        $this->recordedEvents = [];

        return $pendingEvents;
    }

    /**
     * Replay past events
     *
     * @param object[] $historyEvents
     *
     * @throws \RuntimeException
     * @return void
     */
    private function replay(array $historyEvents)
    {
        foreach ($historyEvents as $pastEvent) {
            $this->apply($pastEvent);
        }
    }

    /**
     * Record an aggregate changed DomainEvent
     *
     * @param object $event
     */
    private function recordThat($event)
    {
        $this->recordedEvents[] = $event;

        $this->apply($event);
    }

    /**
     * Apply given DomainEvent
     *
     * @param object $e
     *
     * @throws \RuntimeException
     */
    private function apply($e)
    {
        $handler = $this->determineEventHandlerMethodFor($e);
        if (! method_exists($this, $handler)) {
            return;
        }

        $this->{$handler}($e);
    }

    /**
     * Determine DomainEvent name
     *
     * @param object $e
     *
     * @return string
     */
    private function determineEventHandlerMethodFor($e)
    {
        return 'when' . join('', array_slice(explode('\\', get_class($e)), -1));
    }
}
