<?php

namespace PhpInPractice\Matters\Aggregate\StreamNameGenerator;

/**
 * @coversDefaultClass PhpInPractice\Matters\Aggregate\StreamNameGenerator\SluggifiedNameAndId
 * @covers ::<private>
 */
class SluggifiedNameAndIdTest extends \PHPUnit_Framework_TestCase
{
    /** @var SluggifiedNameAndId */
    private $generator;

    public function setUp()
    {
        $this->generator = new SluggifiedNameAndId();
    }

    /**
     * @test
     * @covers ::generate
     */
    public function it_should_make_a_slug_with_the_aggregate_class_and_id()
    {
        $this->assertSame('my.project-1', $this->generator->generate("\\My\\Project", '1'));
        $this->assertSame('my.project-1', $this->generator->generate("My\\Project\\", '1'));
        $this->assertSame('my.project-1', $this->generator->generate("My/Project", '1'));
        $this->assertSame('my.project-1', $this->generator->generate(" \t\n/\\My\\Project\\/\t\n ", '1'));
    }
}
