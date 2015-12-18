<?php

namespace PhpInPractice\Matters\Aggregate\Transaction;

use EventStore\EventStoreInterface;
use EventStore\ValueObjects\Identity\UUID;
use EventStore\WritableEvent;
use EventStore\WritableEventCollection;
use Mockery as m;

/**
 * @coversDefaultClass PhpInPractice\Matters\Aggregate\Transaction\SingleStream
 * @covers ::<private>
 * @covers ::__construct
 */
class SingleStreamTest extends \PHPUnit_Framework_TestCase
{
    /** @var EventStoreInterface|m\MockInterface */
    private $eventstore;

    /** @var None */
    private $transaction;

    public function setUp()
    {
        $this->eventstore  = m::mock(EventStoreInterface::class);
        $this->transaction = new SingleStream($this->eventstore);
    }

    /**
     * @test
     * @covers ::push
     */
    public function it_should_not_write_events_to_eventstore_on_push()
    {
        $expectedEvents = [new WritableEvent(new UUID(), '123', [])];
        $this->eventstore->shouldReceive('writeToStream')->never();

        $this->transaction->push('streamUri', $expectedEvents);
    }

    /**
     * @test
     * @covers ::commit
     * @covers ::push
     */
    public function it_should_write_events_to_eventstore_on_flush()
    {
        $expectedEvents = [new WritableEvent(new UUID(), '123', [])];
        $this->eventstore->shouldReceive('writeToStream')->once()->with('streamUri', m::type(WritableEventCollection::class));
        $this->transaction->push('streamUri', $expectedEvents);

        $this->transaction->commit();
    }

    /**
     * @test
     * @covers ::commit
     * @covers ::push
     */
    public function it_should_not_commit_if_there_are_no_events()
    {
        $this->eventstore->shouldReceive('writeToStream')->never();
        $this->transaction->push('streamUri', []);

        $this->transaction->commit();
    }

    /**
     * @test
     * @covers ::rollback
     */
    public function it_should_clear_events_on_rollback()
    {
        $expectedEvents = [new WritableEvent(new UUID(), '123', [])];
        $this->transaction->push('streamUri', $expectedEvents);

        $this->transaction->rollback();

        $this->assertAttributeCount(0, 'events', $this->transaction);
        $this->assertAttributeSame('', 'streamUri', $this->transaction);
    }

    /**
     * @test
     * @covers ::push
     * @expectedException \PhpInPractice\Matters\Aggregate\Transaction\TransactionLimitedToASingleStreamException
     */
    public function it_should_throw_exception_when_pushing_events_from_multiple_streams()
    {
        $expectedEvents = [new WritableEvent(new UUID(), '123', [])];

        $this->transaction->push('streamUri', $expectedEvents);
        $this->transaction->push('streamUri2', $expectedEvents);
    }
}
