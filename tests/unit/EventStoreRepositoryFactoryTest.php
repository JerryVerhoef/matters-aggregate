<?php

namespace PhpInPractice\Matters\Aggregate;

use EventStore\EventStoreInterface;
use Mockery as m;

/**
 * @coversDefaultClass PhpInPractice\Matters\Aggregate\EventStoreRepositoryFactory
 * @covers ::<private>
 * @covers ::__construct
 */
class EventStoreRepositoryFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $metadataStore;
    /** @var EventStoreInterface|m\MockInterface */
    private $eventStore;

    /** @var StreamNameGenerator|m\MockInterface */
    private $streamNameGenerator;

    /** @var EventSerializer|m\MockInterface */
    private $eventSerializer;

    /** @var Transaction|m\MockInterface */
    private $transaction;

    /** @var RepositoryFactory */
    private $factory;

    public function setUp()
    {
        $this->eventStore          = m::mock(EventStoreInterface::class);
        $this->streamNameGenerator = m::mock(StreamNameGenerator::class);
        $this->eventSerializer     = m::mock(EventSerializer::class);
        $this->transaction         = m::mock(Transaction::class);
        $this->metadataStore       = m::mock(MetadataStore::class);

        $this->factory = new EventStoreRepositoryFactory(
            $this->eventStore,
            $this->streamNameGenerator,
            $this->eventSerializer,
            $this->transaction,
            $this->metadataStore
        );
    }

    /**
     * @test
     * @covers ::create
     */
    public function it_should_create_a_repository_with_the_right_dependencies()
    {
        $expected   = \stdClass::class;
        $repository = $this->factory->create($expected);

        $this->assertAttributeSame($this->streamNameGenerator, 'streamNameGenerator', $repository);
        $this->assertAttributeSame($this->eventStore, 'eventstore', $repository);
        $this->assertAttributeSame($this->transaction, 'transaction', $repository);
        $this->assertAttributeSame($this->eventSerializer, 'eventSerializer', $repository);
        $this->assertAttributeSame($this->metadataStore, 'metadataStore', $repository);
        $this->assertAttributeSame($expected, 'aggregateClassName', $repository);
    }
}
