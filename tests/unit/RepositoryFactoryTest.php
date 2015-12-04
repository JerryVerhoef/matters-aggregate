<?php

namespace PhpInPractice\Matters\Aggregate;

use EventStore\EventStoreInterface;
use Mockery as m;

/**
 * @coversDefaultClass PhpInPractice\Matters\Aggregate\RepositoryFactory
 * @covers ::<private>
 * @covers ::__construct
 */
class RepositoryFactoryTest extends \PHPUnit_Framework_TestCase
{
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

        $this->factory = new RepositoryFactory(
            $this->eventStore,
            $this->streamNameGenerator,
            $this->eventSerializer,
            $this->transaction
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
        $this->assertAttributeSame($expected, 'aggregateClassName', $repository);
    }
}
