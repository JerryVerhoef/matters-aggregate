<?php

namespace PhpInPractice\Matters\Aggregate;

class AggregateMock
{
    use IsEventSourced;

    public function id()
    {
        return 1;
    }

    public function recordEvent($event)
    {
        $this->recordThat($event);
    }
}
