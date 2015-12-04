<?php

namespace PhpInPractice\Matters\Aggregate\EventSerializer;

use Mockery as m;

/**
 * @coversDefaultClass PhpInPractice\Matters\Aggregate\EventSerializer\FromArrayToArray
 * @covers ::<private>
 */
class FromArrayToArrayTest extends \PHPUnit_Framework_TestCase
{
    /** @var FromArrayToArray */
    private $serializer;

    public function setUp()
    {
        $this->serializer = $serializer = new FromArrayToArray();
    }

    /**
     * @test
     * @covers ::serialize
     */
    public function it_should_call_to_array_on_event_when_serializing()
    {
        $expected = ['id' => 1];

        $event = FromArrayToArrayEventMock::fromArray($expected);

        $this->assertSame($expected, $this->serializer->serialize($event));
    }

    /**
     * @test
     * @covers ::unserialize
     */
    public function it_should_call_from_array_on_event_when_unserializing()
    {
        $expected = ['id' => 1];

        $event = $this->serializer->unserialize(FromArrayToArrayEventMock::class, $expected);

        $this->assertInstanceOf(FromArrayToArrayEventMock::class, $event);
        $this->assertSame($expected['id'], $event->id());
    }

    /**
     * @test
     * @covers ::serialize
     * @expectedException \PhpInPractice\Matters\Aggregate\EventSerializer\MissingMethodException
     */
    public function it_should_throw_an_exception_if_the_to_array_method_does_not_exist()
    {
        $this->serializer->serialize(new \stdClass());
    }

    /**
     * @test
     * @covers ::unserialize
     * @expectedException \PhpInPractice\Matters\Aggregate\EventSerializer\MissingMethodException
     */
    public function it_should_throw_an_exception_if_the_from_array_method_does_not_exist()
    {
        $this->serializer->unserialize('stdClass', ['id' => 1]);
    }
}
