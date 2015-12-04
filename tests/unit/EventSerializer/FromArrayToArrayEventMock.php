<?php

namespace PhpInPractice\Matters\Aggregate\EventSerializer;

class FromArrayToArrayEventMock implements EventSerializesUsingFromAndToArrayMethods
{
    private $id;

    public function id()
    {
        return $this->id;
    }

    public function toArray()
    {
        return [ 'id' => $this->id ];
    }

    public static function fromArray(array $data)
    {
        $event = new static();
        $event->id = $data['id'];

        return $event;
    }
}
