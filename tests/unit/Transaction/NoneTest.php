<?php

namespace PhpInPractice\Matters\Aggregate\Transaction;

use EventStore\EventStoreInterface;
use EventStore\ValueObjects\Identity\UUID;
use EventStore\WritableEvent;
use EventStore\WritableEventCollection;
use Mockery as m;

/**
 * @coversDefaultClass PhpInPractice\Matters\Aggregate\Transaction\None
 * @covers ::<private>
 * @covers ::__construct
 */
class NoneTest extends \PHPUnit_Framework_TestCase
{
    /** @var EventStoreInterface|m\MockInterface */
    private $eventstore;

    /** @var None */
    private $transaction;

    public function setUp()
    {
        $this->eventstore  = m::mock(EventStoreInterface::class);
        $this->transaction = new None($this->eventstore);
    }

    /**
     * @test
     * @covers ::push
     */
    public function it_should_immediately_write_events_to_eventstore()
    {
        $expectedEvents = [new WritableEvent(new UUID(), '123', [])];
        $this->eventstore->shouldReceive('writeToStream')->with('streamUri', m::type(WritableEventCollection::class));

        $this->transaction->push('streamUri', $expectedEvents);
    }

    /**
     * @test
     * @covers ::commit
     */
    public function it_should_do_nothing_on_commit()
    {
        $this->eventstore->shouldReceive('writeToStream')->never();

        $this->transaction->commit();
    }

    /**
     * @test
     * @covers ::rollback
     */
    public function it_should_do_nothing_on_rollback()
    {
        $this->transaction->rollback();
    }
}
