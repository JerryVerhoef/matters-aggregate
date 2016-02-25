<?php

namespace PhpInPractice\Matters\Aggregate;

use EventStore\EventStoreInterface;
use EventStore\StreamFeed\Entry;
use EventStore\StreamFeed\EntryWithEvent;
use EventStore\StreamFeed\Event;
use EventStore\WritableEvent;
use Mockery as m;

/**
 * @coversDefaultClass PhpInPractice\Matters\Aggregate\EventStoreRepository
 * @covers ::<private>
 * @covers ::__construct
 */
class EventStoreRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var MetadataStore|m\MockInterface */
    private $metadataStore;

    /** @var EventStoreInterface|m\MockInterface */
    private $eventStore;

    /** @var StreamNameGenerator|m\MockInterface */
    private $streamNameGenerator;

    /** @var EventSerializer|m\MockInterface */
    private $eventSerializer;

    /** @var Transaction|m\MockInterface */
    private $transaction;

    /** @var Repository */
    private $repository;

    public function setUp()
    {
        $this->eventStore          = m::mock(EventStoreInterface::class);
        $this->streamNameGenerator = m::mock(StreamNameGenerator::class);
        $this->eventSerializer     = m::mock(EventSerializer::class);
        $this->transaction         = m::mock(Transaction::class);
        $this->metadataStore       = m::mock(MetadataStore::class);

        $this->repository = new EventStoreRepository(
            $this->streamNameGenerator,
            $this->eventSerializer,
            $this->eventStore,
            $this->transaction,
            AggregateMock::class,
            $this->metadataStore
        );
    }

    /**
     * @test
     * @covers ::persist
     */
    public function it_should_persist_an_aggregate_root()
    {
        $aggregate = new AggregateMock();
        $exampleEvent = m::mock(\stdClass::class);

        $aggregate->recordEvent($exampleEvent);
        $this->streamNameGenerator->shouldReceive('generate')->andReturn('stream.id');
        $this->eventSerializer->shouldReceive('serialize')->with($exampleEvent)->andReturn(['id' => 2]);

        $this->transaction
            ->shouldReceive('push')
            ->once()
            ->with(
                'stream.id',
                m::on(function ($events) use ($exampleEvent) {
                    /** @var WritableEvent $event */
                    $event = current($events);
                    $data = $event->toStreamData();
                    $this->assertSame(get_class($exampleEvent), $data['eventType']);
                    $this->assertSame(['id' => 2], $data['data']);
                    $this->assertSame(['user_id' => 1], $data['metadata']);
                    return true;
                })
            );

        $this->metadataStore->shouldReceive('metadata')
            ->with($exampleEvent)->andReturn(['user_id' => 1]);

        $this->repository->persist($aggregate);
    }

    /**
     * @test
     * @covers ::findById
     */
    public function it_should_reconstitute_state_based_on_events()
    {
        $event = new Event('test', 1, []);
        $entryWithEvent = new EntryWithEvent(new Entry([]), $event);
        $expectedEntity = new \stdClass();

        $this->streamNameGenerator->shouldReceive('generate')->andReturn('stream.id');
        $this->eventStore
            ->shouldReceive('forwardStreamFeedIterator')
            ->andReturn([$entryWithEvent]);
        $this->eventSerializer->shouldReceive('unserialize')
            ->with('test', [])
            ->once()
            ->andReturn($expectedEntity);

        $this->assertEquals(new AggregateMock(), $this->repository->findById('1'));
    }

    /**
     * @test
     * @covers ::findById
     */
    public function it_should_return_null_if_there_are_no_events_for_the_given_entity()
    {
        $streamUri = 'stream.id';
        $this->streamNameGenerator->shouldReceive('generate')->andReturn($streamUri);
        $this->eventStore->shouldReceive('forwardStreamFeedIterator')
            ->once()
            ->with($streamUri)
            ->andReturn([]);

        $this->assertNull($this->repository->findById('1'));
    }
}
