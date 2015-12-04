<?php

namespace PhpInPractice\Matters\Aggregate\EventSerializer;

interface EventSerializesUsingFromAndToArrayMethods
{
    public function toArray();
    public static function fromArray(array $data);
}
