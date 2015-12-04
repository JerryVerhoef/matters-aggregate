<?php

namespace PhpInPractice\Matters\Aggregate;

interface EventSerializer
{
    public function serialize($object);
    public function unserialize($class, array $data);
}
